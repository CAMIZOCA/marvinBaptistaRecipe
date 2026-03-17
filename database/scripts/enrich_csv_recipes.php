<?php
/**
 * Enriches the 20 Chinese recipes imported from CSV with:
 * - description (auto-generated from preparacion text)
 * - subtitle  (pais · dificultad · porciones)
 * - prep_time_minutes / cook_time_minutes (extracted from text via regex)
 * - category link (from pais field)
 * - corrected ingredient units (lowercase)
 * - removes "title-as-ingredient" artifact from CSV data
 *
 * Run with Laragon terminal:
 *   php artisan tinker --execute="require database_path('scripts/enrich_csv_recipes.php');"
 */

$csvPath = 'D:/Personal/descargas/recetas_chinas_tradicionales_wp_import (2).csv';

if (!file_exists($csvPath)) {
    echo "ERROR: CSV not found at {$csvPath}\n";
    return;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function _diffLower(string $d): string {
    return match (mb_strtolower(trim($d))) {
        'fácil', 'facil', 'easy'     => 'fácil',
        'difícil', 'dificil', 'hard' => 'difícil',
        default                      => 'media',
    };
}
function _diffCap(string $d): string {
    return match (mb_strtolower(trim($d))) {
        'fácil', 'facil', 'easy'     => 'Fácil',
        'difícil', 'dificil', 'hard' => 'Difícil',
        default                      => 'Media',
    };
}

function _buildDescription(string $title, string $prep, string $pais, string $dif): string {
    $dl    = _diffLower($dif);
    $first = '';
    foreach (preg_split('/\r?\n/', $prep) as $line) {
        $clean = preg_replace('/^\d+[\.\)]\s+/', '', trim($line));
        if (strlen($clean) > 20) {
            $parts = preg_split('/(?<=[.!?])\s+/', $clean, 2);
            $first = trim($parts[0]);
            break;
        }
    }
    $intro = "Aprende a preparar {$title}, una deliciosa receta de dificultad {$dl}"
           . ($pais ? " con sabores de {$pais}." : '.');
    return $first ? "{$intro} {$first}" : $intro;
}

function _buildSubtitle(string $pais, string $dif, int $por): string {
    $parts = [];
    if ($pais) $parts[] = $pais;
    if ($dif)  $parts[] = _diffCap($dif);
    if ($por)  $parts[] = "{$por} porciones";
    return implode(' · ', $parts);
}

function _extractTimes(string $text): array {
    if (!$text) return [15, 0];
    preg_match_all('/(\d+(?:[,\.]\d+)?)\s*(hora[s]?|h\b|minuto[s]?|min\b)/iu', $text, $m, PREG_SET_ORDER);
    $total = 0;
    foreach ($m as $match) {
        $v = (float) str_replace(',', '.', $match[1]);
        $total += (str_starts_with(mb_strtolower($match[2]), 'h') ? $v * 60 : $v);
    }
    $total = (int) min($total, 600);
    if ($total <= 30)  return [10, $total > 0 ? $total : 20];
    if ($total <= 90)  return [15, $total - 15];
    return [20, $total - 20];
}

// ── Parse CSV ─────────────────────────────────────────────────────────────────

$handle = fopen($csvPath, 'r');
// Detect and remove BOM
$bom = fread($handle, 3);
if ($bom !== "\xEF\xBB\xBF") {
    rewind($handle);
}
$header = fgetcsv($handle); // skip header row
$rows   = [];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 8) continue;
    $rows[] = [
        'titulo'     => $row[0],
        'pais'       => $row[1],
        'dificultad' => $row[2],
        'porciones'  => (int) ($row[5] ?? 0),
        'ingredientes' => $row[6] ?? '',
        'preparacion'  => $row[7] ?? '',
    ];
}
fclose($handle);

echo "Parsed " . count($rows) . " rows from CSV\n\n";

// ── Process each row ──────────────────────────────────────────────────────────

$ok   = 0;
$skip = 0;

foreach ($rows as $row) {
    $title = trim($row['titulo']);
    // Try exact match first, then with -1 suffix (duplicate slugs from earlier imports)
    $recipe = \App\Models\Recipe::where('title', $title)->first();

    if (!$recipe) {
        echo "  [SKIP] Not found in DB: {$title}\n";
        $skip++;
        continue;
    }

    $pais  = trim($row['pais']);
    $dif   = trim($row['dificultad']);
    $prep  = trim($row['preparacion']);
    $ings  = trim($row['ingredientes']);
    $por   = $row['porciones'];

    [$prepTime, $cookTime] = _extractTimes($prep);
    $description = _buildDescription($title, $prep, $pais, $dif);
    $subtitle    = _buildSubtitle($pais, $dif, $por);

    // Update main recipe fields
    $recipe->update([
        'description'       => $description,
        'subtitle'          => $subtitle,
        'prep_time_minutes' => $prepTime,
        'cook_time_minutes' => $cookTime,
    ]);

    // Ensure category is linked
    if ($pais) {
        $catSlug = \Illuminate\Support\Str::slug($pais);
        $cat = \App\Models\Category::firstOrCreate(
            ['slug' => $catSlug],
            ['name' => $pais, 'sort_order' => 99]
        );
        if (!$recipe->categories()->where('categories.id', $cat->id)->exists()) {
            $recipe->categories()->attach($cat->id, ['is_primary' => true]);
        }
    }

    // Fix ingredients
    $titleNorm = mb_strtolower($title);
    foreach ($recipe->ingredients as $ing) {
        // Remove if ingredient name is the recipe title (CSV header artifact)
        if (mb_strtolower(trim($ing->ingredient_name ?? '')) === $titleNorm) {
            $ing->delete();
            continue;
        }
        // Normalize unit to lowercase
        if ($ing->unit && $ing->unit !== mb_strtolower($ing->unit)) {
            $ing->update(['unit' => mb_strtolower($ing->unit)]);
        }
    }

    // Re-index order_position after any deletions
    $pos = 1;
    foreach ($recipe->ingredients()->orderBy('order_position')->get() as $ing) {
        $ing->update(['order_position' => $pos++]);
    }

    $descLen = strlen($description);
    $ingCount = $recipe->ingredients()->count();
    echo "  [OK] {$title}\n";
    echo "       prep={$prepTime}m cook={$cookTime}m | ingredients={$ingCount} | desc={$descLen}chars\n";
    $ok++;
}

echo "\n──────────────────────────────────\n";
echo "Enriched: {$ok} | Skipped: {$skip}\n";
