<?php

namespace App\Jobs;

use App\Models\AmazonBook;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\Tag;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportRecipeChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;

    private const UNITS = [
        'litro','litros','l',
        'mililitro','mililitros','ml',
        'gramo','gramos','gr','g',
        'kilogramo','kilogramos','kg',
        'libra','libras','lb',
        'onza','onzas','oz',
        'cucharada','cucharadas','cuchara','cucharas',
        'cucharadita','cucharaditas','cucharilla','cucharillas',
        'taza','tazas',
        'pizca','pizcas',
        'chorrito',
        'lata','latas',
        'paquete','paquetes',
        'ramita','ramitas','rama','ramas',
        'rebanada','rebanadas',
        'rodaja','rodajas',
        'trozo','trozos',
        'punado','puñado',
        'diente','dientes',
        'hoja','hojas',
        'disco','discos',
    ];

    public function __construct(
        private array $rows,
        private int   $userId,
    ) {}

    public function handle(): void
    {
        foreach ($this->rows as $row) {
            if ($this->batch()?->cancelled()) return;
            try {
                $this->importRow($row);
            } catch (\Throwable $e) {
                logger()->error('CSV Import row failed: ' . $e->getMessage(), [
                    'title' => $row['titulo'] ?? $row['title'] ?? '?',
                ]);
            }
        }
    }

    private function importRow(array $row): void
    {
        // Auto-detect format: new (titulo/pais) vs old (title/origin_country)
        if (array_key_exists('titulo', $row)) {
            $this->importNewFormat($row);
        } else {
            $this->importOldFormat($row);
        }
    }

    /* ══════════════════════════════════════════════════════════
     *  NEW FORMAT: titulo, pais, dificultad, ingredientes (multiline),
     *              preparacion (numbered), amazon_link, imagen_url
     * ══════════════════════════════════════════════════════════ */
    private function importNewFormat(array $row): void
    {
        DB::transaction(function () use ($row) {
            $title = trim($row['titulo'] ?? 'Sin título');

            if (Recipe::where('title', $title)->exists()) return;

            $recipe = Recipe::create([
                'title'             => $title,
                'origin_country'    => trim($row['pais'] ?? ''),
                'difficulty'        => $this->mapDifficulty($row['dificultad'] ?? ''),
                'prep_time_minutes' => $this->parseInt($row['tiempo_preparacion'] ?? 0),
                'cook_time_minutes' => $this->parseInt($row['tiempo_coccion'] ?? 0),
                'servings'          => $this->parseInt($row['porciones'] ?? 0) ?: null,
                'is_published'      => false,
                'user_id'           => $this->userId,
            ]);

            // Ingredients (multiline free text)
            if (!empty($row['ingredientes'])) {
                $this->parseAndSaveIngredients($recipe, $row['ingredientes']);
            }

            // Steps (numbered list)
            if (!empty($row['preparacion'])) {
                $this->parseAndSaveSteps($recipe, $row['preparacion']);
            }

            // Featured image from Google Drive
            if (!empty($row['imagen_url'])) {
                $imagePath = $this->downloadImage($row['imagen_url'], $recipe->slug);
                if ($imagePath) {
                    $recipe->update(['featured_image' => $imagePath]);
                }
            }

            // Amazon book: find/create and link
            if (!empty($row['amazon_link'])) {
                $book = $this->findOrCreateBook($row['amazon_link']);
                if ($book) {
                    $recipe->books()->syncWithoutDetaching([
                        $book->id => ['relevance_type' => 'source'],
                    ]);
                }
            }
        });
    }

    /* ══════════════════════════════════════════════════════════
     *  OLD FORMAT: title|subtitle|..., ingredients (pipe), steps (pipe)
     * ══════════════════════════════════════════════════════════ */
    private function importOldFormat(array $row): void
    {
        DB::transaction(function () use ($row) {
            $title = $row['title'] ?? 'Sin título';
            if (Recipe::where('title', $title)->exists()) return;

            $recipe = Recipe::create([
                'title'             => $title,
                'subtitle'          => $row['subtitle'] ?? null,
                'description'       => $row['description'] ?? null,
                'origin_country'    => $row['origin_country'] ?? null,
                'prep_time_minutes' => $this->parseInt($row['prep_time'] ?? 0),
                'cook_time_minutes' => $this->parseInt($row['cook_time'] ?? 0),
                'servings'          => $this->parseInt($row['servings'] ?? 0) ?: null,
                'difficulty'        => in_array($row['difficulty'] ?? '', ['easy','medium','hard'])
                    ? $row['difficulty'] : 'medium',
                'story'             => $row['story'] ?? null,
                'tips_secrets'      => $row['tips'] ?? null,
                'is_published'      => false,
                'user_id'           => $this->userId,
            ]);

            if (!empty($row['category'])) {
                $cat = Category::firstOrCreate(
                    ['slug' => Str::slug($row['category'])],
                    ['name' => $row['category']]
                );
                $recipe->categories()->attach($cat->id, ['is_primary' => true]);
            }

            if (!empty($row['tags'])) {
                $tagIds = [];
                foreach (array_map('trim', explode(',', $row['tags'])) as $tagName) {
                    if (!$tagName) continue;
                    $tag = Tag::firstOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $recipe->tags()->sync($tagIds);
            }

            if (!empty($row['ingredients'])) {
                $pos = 0;
                foreach (array_map('trim', explode(';', $row['ingredients'])) as $line) {
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    RecipeIngredient::create([
                        'recipe_id'        => $recipe->id,
                        'order_position'   => ++$pos,
                        'amount'           => is_numeric($parts[0] ?? null) ? (float) $parts[0] : null,
                        'unit'             => $parts[1] ?? null,
                        'ingredient_name'  => $parts[2] ?? $line,
                        'ingredient_group' => $parts[3] ?? null,
                    ]);
                }
            }

            if (!empty($row['steps'])) {
                $num = 0;
                foreach (array_map('trim', explode(';', $row['steps'])) as $line) {
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    RecipeStep::create([
                        'recipe_id'   => $recipe->id,
                        'step_number' => ++$num,
                        'title'       => count($parts) > 1 ? $parts[0] : null,
                        'description' => count($parts) > 1 ? $parts[1] : $parts[0],
                    ]);
                }
            }
        });
    }

    /* ══════════════════════════════════════════════════════════
     *  INGREDIENT PARSER
     *  "30 Naranjas kumquat"          → {amount:30, unit:null, name:"Naranjas kumquat"}
     *  "1 litro de Aguardiente seco"  → {amount:1,  unit:"litro", name:"Aguardiente seco"}
     *  "½ litro de Agua"              → {amount:0.5,unit:"litro", name:"Agua"}
     * ══════════════════════════════════════════════════════════ */
    private function parseAndSaveIngredients(Recipe $recipe, string $raw): void
    {
        $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $raw))));
        $pos   = 0;
        foreach ($lines as $line) {
            if (!$line) continue;
            [$amount, $unit, $name] = $this->parseIngredientLine($line);
            RecipeIngredient::create([
                'recipe_id'       => $recipe->id,
                'order_position'  => ++$pos,
                'amount'          => $amount,
                'unit'            => $unit,
                'ingredient_name' => $name,
            ]);
        }
    }

    private function parseIngredientLine(string $line): array
    {
        // Normalize vulgar fractions to decimals
        $line = strtr($line, ['½' => '0.5', '¼' => '0.25', '¾' => '0.75', '⅓' => '0.33', '⅔' => '0.67', '⅛' => '0.125']);

        $tokens = preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($tokens)) return [null, null, $line];

        $amount = null;
        $offset = 0;

        // First token = amount?
        if (preg_match('/^[\d]+([\.,]\d+)?(\/\d+)?$/', $tokens[0])) {
            $amount = (float) str_replace(',', '.', $tokens[0]);
            $offset = 1;
        }

        // Next token = unit?
        $unit      = null;
        $nameStart = $offset;
        if (isset($tokens[$offset])) {
            $candidate = mb_strtolower($tokens[$offset]);
            if (in_array($candidate, self::UNITS)) {
                $unit      = $tokens[$offset];
                $nameStart = $offset + 1;
                // Skip "de" connector
                if (isset($tokens[$nameStart]) && mb_strtolower($tokens[$nameStart]) === 'de') {
                    $nameStart++;
                }
            }
        }

        $name = implode(' ', array_slice($tokens, $nameStart)) ?: $line;

        return [$amount, $unit, $name];
    }

    /* ══════════════════════════════════════════════════════════
     *  STEP PARSER
     *  "1. texto\n2. otro texto"  →  numbered steps
     * ══════════════════════════════════════════════════════════ */
    private function parseAndSaveSteps(Recipe $recipe, string $raw): void
    {
        $lines      = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $raw))));
        $stepNumber = 0;

        foreach ($lines as $line) {
            if (!$line) continue;
            if (preg_match('/^\d+[\.\)]\s+(.+)$/', $line, $m)) {
                $stepNumber++;
                RecipeStep::create([
                    'recipe_id'   => $recipe->id,
                    'step_number' => $stepNumber,
                    'title'       => 'Paso ' . $stepNumber,
                    'description' => trim($m[1]),
                ]);
            } elseif ($stepNumber === 0) {
                $stepNumber++;
                RecipeStep::create([
                    'recipe_id'   => $recipe->id,
                    'step_number' => $stepNumber,
                    'title'       => 'Paso 1',
                    'description' => $line,
                ]);
            }
        }
    }

    /* ══════════════════════════════════════════════════════════
     *  IMAGE DOWNLOADER (Google Drive & generic URLs)
     * ══════════════════════════════════════════════════════════ */
    private function downloadImage(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; RecipeImporter/1.0)'])
                ->get($url);

            if (!$response->successful()) return null;

            $body        = $response->body();
            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            $ext         = str_contains($contentType, 'png') ? 'png'
                         : (str_contains($contentType, 'webp') ? 'webp' : 'jpg');

            $directory = storage_path('app/public/recipes');
            if (!is_dir($directory)) mkdir($directory, 0755, true);

            $tempFile = $directory . DIRECTORY_SEPARATOR . Str::slug($slug) . '-tmp.' . $ext;
            file_put_contents($tempFile, $body);

            $optimized = $this->optimizeImage($tempFile, $directory, $slug);
            return $optimized ? '/storage/recipes/' . basename($optimized) : null;

        } catch (\Throwable $e) {
            logger()->warning('Image download failed: ' . $e->getMessage(), ['url' => $url]);
            return null;
        }
    }

    private function optimizeImage(string $sourcePath, string $directory, string $slug): ?string
    {
        try {
            $info = @getimagesize($sourcePath);
            if (!$info) return null;

            [$origWidth, $origHeight, $type] = $info;

            $src = match ($type) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG  => @imagecreatefrompng($sourcePath),
                IMAGETYPE_GIF  => @imagecreatefromgif($sourcePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
                default        => null,
            };

            if (!$src) return null;

            // Resize to max 1200x800 keeping ratio
            $ratio     = min(1200 / $origWidth, 800 / $origHeight, 1);
            $newW      = max(1, (int) ($origWidth  * $ratio));
            $newH      = max(1, (int) ($origHeight * $ratio));
            $dest      = imagecreatetruecolor($newW, $newH);

            if ($type === IMAGETYPE_PNG) {
                imagealphablending($dest, false);
                imagesavealpha($dest, true);
            }

            imagecopyresampled($dest, $src, 0, 0, 0, 0, $newW, $newH, $origWidth, $origHeight);

            $outFile = $directory . DIRECTORY_SEPARATOR . Str::slug($slug) . '-opt.jpg';
            imagejpeg($dest, $outFile, 82);

            imagedestroy($src);
            imagedestroy($dest);

            if ($sourcePath !== $outFile && file_exists($sourcePath)) unlink($sourcePath);

            return $outFile;
        } catch (\Throwable $e) {
            logger()->warning('Image optimize failed: ' . $e->getMessage());
            return null;
        }
    }

    /* ══════════════════════════════════════════════════════════
     *  AMAZON BOOK FINDER / CREATOR
     *  URL: https://www.amazon.com/dp/B0GMWZ6K3F?tag=...
     * ══════════════════════════════════════════════════════════ */
    private function findOrCreateBook(string $url): ?AmazonBook
    {
        if (!preg_match('/(?:\/dp\/|\/gp\/product\/)([A-Z0-9]{10})/', $url, $m)) return null;

        $asin = $m[1];
        $tag  = config('services.amazon.affiliate_tag', 'marvinbaptista-20');

        return AmazonBook::firstOrCreate(
            ['asin' => $asin],
            [
                'title'         => 'Libro de Recetas (ASIN: ' . $asin . ')',
                'amazon_url_us' => "https://www.amazon.com/dp/{$asin}?tag={$tag}",
                'amazon_url_mx' => "https://www.amazon.com.mx/dp/{$asin}?tag={$tag}",
                'amazon_url_es' => "https://www.amazon.es/dp/{$asin}?tag={$tag}",
                'amazon_url_ar' => "https://www.amazon.com.ar/dp/{$asin}?tag={$tag}",
                'is_active'     => true,
                'cuisine_type'  => 'Internacional',
            ]
        );
    }

    /* ══════════════════════════════════════════════════════════
     *  HELPERS
     * ══════════════════════════════════════════════════════════ */
    private function mapDifficulty(string $val): string
    {
        return match (mb_strtolower(trim($val))) {
            'fácil', 'facil', 'easy'     => 'easy',
            'difícil', 'dificil', 'hard' => 'hard',
            default                      => 'medium',
        };
    }

    private function parseInt(mixed $val): int
    {
        return is_numeric($val) ? (int) $val : 0;
    }
}
