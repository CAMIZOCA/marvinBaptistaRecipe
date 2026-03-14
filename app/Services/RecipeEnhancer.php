<?php

namespace App\Services;

use App\Models\AmazonBook;
use App\Models\Recipe;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class RecipeEnhancer
{
    public function enhance(Recipe $recipe): array
    {
        $recipe->load('ingredients', 'steps', 'categories', 'tags');

        $prompt   = $this->buildUserPrompt($recipe);
        $provider = Setting::get('ai_provider', 'anthropic');

        $content = $provider === 'local'
            ? $this->callLocalAi($prompt)
            : $this->callAnthropic($prompt);

        // Extract JSON if wrapped in markdown code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = $matches[1];
        }

        $result = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('La IA devolvió una respuesta inválida. Por favor intenta nuevamente.');
        }

        return $this->validateAndClean($result);
    }

    /* ─── Anthropic (Claude) ──────────────────────────────────────── */

    private function callAnthropic(string $prompt): string
    {
        $apiKey = Setting::get('anthropic_api_key') ?: config('services.anthropic.key');
        $model  = Setting::get('anthropic_model')   ?: config('ai.anthropic.model');

        if (blank($apiKey)) {
            throw new \RuntimeException(
                'No hay clave API de Anthropic configurada. Ve a Ajustes → IA y agrega tu clave.'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => config('ai.anthropic.version', '2023-06-01'),
            'content-type'      => 'application/json',
        ])
        ->timeout(config('ai.anthropic.timeout', 90))
        ->post(config('ai.anthropic.api_url', 'https://api.anthropic.com/v1/messages'), [
            'model'      => $model,
            'max_tokens' => config('ai.anthropic.max_tokens', 4096),
            'system'     => config('ai.system_prompt'),
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Error Anthropic ' . $response->status() . ': ' . ($response->json('error.message') ?? $response->body())
            );
        }

        return $response->json('content.0.text', '');
    }

    /* ─── Local AI (OpenAI-compatible: Ollama, LM Studio, Jan…) ───── */

    private function callLocalAi(string $prompt): string
    {
        $url   = rtrim(Setting::get('local_ai_url', 'http://localhost:11434'), '/');
        $model = Setting::get('local_ai_model', 'llama3.2');
        $key   = Setting::get('local_ai_api_key', 'local');  // many servers accept any string

        if (blank($url)) {
            throw new \RuntimeException(
                'No hay URL de IA local configurada. Ve a Ajustes → IA.'
            );
        }

        // Ensure endpoint ends with /v1/chat/completions (OpenAI-compatible)
        if (!str_ends_with($url, '/chat/completions')) {
            $url = rtrim($url, '/') . '/v1/chat/completions';
        }

        $systemPrompt = config('ai.system_prompt',
            'Eres un experto consultor de SEO para blogs de cocina. Responde siempre en español con JSON válido.'
        );

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . ($key ?: 'local'),
        ])
        ->timeout(180)
        ->post($url, [
            'model'    => $model,
            'stream'   => false,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $prompt],
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Error IA local ' . $response->status() . ': ' . $response->body()
            );
        }

        // OpenAI-compatible response
        return $response->json('choices.0.message.content', '');
    }

    /* ─── Apply enhancements ──────────────────────────────────────── */

    public function applyEnhancement(Recipe $recipe, array $fields): void
    {
        $allowedFields = ['seo_title', 'seo_description', 'story', 'tips_secrets'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($fields[$field]) && $fields[$field]) {
                $updateData[$field] = $fields[$field];
            }
        }

        $updateData['ai_enhanced_at'] = now();
        $recipe->update($updateData);

        // Handle FAQs if included
        if (!empty($fields['faq']) && is_array($fields['faq'])) {
            $recipe->faqs()->delete();
            foreach ($fields['faq'] as $i => $faqItem) {
                $recipe->faqs()->create([
                    'question'   => $faqItem['question'] ?? '',
                    'answer'     => $faqItem['answer'] ?? '',
                    'sort_order' => $i + 1,
                ]);
            }
        }

        // Match Amazon books by keywords
        if (!empty($fields['amazon_keywords']) && is_array($fields['amazon_keywords'])) {
            $this->matchAmazonBooks($recipe, $fields['amazon_keywords']);
        }
    }

    private function matchAmazonBooks(Recipe $recipe, array $keywords): void
    {
        $matchedBookIds = [];

        foreach ($keywords as $keyword) {
            $books = AmazonBook::where('is_active', true)
                ->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('author', 'like', '%' . $keyword . '%')
                      ->orWhere('cuisine_type', 'like', '%' . $keyword . '%');
                })
                ->take(2)
                ->get();

            foreach ($books as $book) {
                $matchedBookIds[$book->id] = ['relevance_type' => 'ai_matched'];
            }
        }

        $existingPivot = $recipe->books()->pluck('recipe_books.relevance_type', 'amazon_books.id');
        foreach ($existingPivot as $bookId => $relevanceType) {
            if ($relevanceType !== 'ai_matched') {
                $matchedBookIds[$bookId] = ['relevance_type' => $relevanceType];
            }
        }

        $recipe->books()->sync($matchedBookIds);
    }

    private function buildUserPrompt(Recipe $recipe): string
    {
        $ingredients = $recipe->ingredients->map(function ($ing) {
            $parts = [];
            if ($ing->amount) $parts[] = $ing->amount;
            if ($ing->unit)   $parts[] = $ing->unit;
            $parts[] = $ing->ingredient_name;
            return implode(' ', $parts);
        })->implode(', ');

        $steps = $recipe->steps->map(function ($step) {
            return 'Paso ' . $step->step_number . ': ' . strip_tags($step->description);
        })->implode("\n");

        $category   = $recipe->categories->first()?->name ?? 'sin categoría';
        $tags       = $recipe->tags->pluck('name')->implode(', ') ?: 'sin etiquetas';
        $country    = trim($recipe->origin_country ?? 'desconocido');
        $region     = trim($recipe->origin_region ?? '');
        $origin     = $region ? "{$country} (región: {$region})" : $country;
        $difficulty = $recipe->difficulty ?? 'media';
        $prepTime   = ($recipe->prep_time_minutes ?? 0) + ($recipe->cook_time_minutes ?? 0);

        return <<<PROMPT
Analiza esta receta y genera contenido SEO de máxima calidad en español correcto y fluido.

=== DATOS DE LA RECETA ===
Título: {$recipe->title}
Origen: {$origin}
Categoría: {$category}
Etiquetas: {$tags}
Dificultad: {$difficulty}
Tiempo total: {$prepTime} minutos
Descripción actual: {$recipe->description}
Ingredientes: {$ingredients}

Preparación:
{$steps}

=== INSTRUCCIONES POR CAMPO ===

1. seo_title (OBLIGATORIO):
   - Máximo 60 caracteres exactos, cuenta los espacios
   - Incluye la keyword principal (nombre del plato)
   - Formato preferido: "Receta de [Plato] [Adjetivo o Beneficio]"
   - Ejemplo correcto: "Receta de Ceviche Peruano Auténtico: Fácil y Refrescante"
   - NO uses null. NO dejes vacío.

2. seo_description (OBLIGATORIO):
   - Entre 140 y 155 caracteres exactos, cuenta los espacios
   - Incluye: keyword principal + beneficio concreto + llamada a la acción
   - Ejemplo correcto: "Aprende a preparar {$recipe->title} con esta receta tradicional de {$country}. Ingredientes sencillos, técnica de chef y sabor auténtico garantizado."
   - NO uses null. NO dejes vacío. CUENTA los caracteres antes de responder.

3. story (OBLIGATORIO):
   - Mínimo 500 palabras, máximo 650 palabras
   - Escrito en primera persona plural como chef con experiencia en {$country}
   - Estructura: párrafo de enganche → origen histórico/cultural documentable → evolución del plato → significado en la gastronomía local → cómo lo descubrió el autor → invitación al lector
   - Debe ser factualmente correcto: menciona regiones reales, épocas históricas aproximadas, técnicas tradicionales verificables
   - Tono: cálido, apasionado, educativo; apto para alguien que prueba este plato por primera vez
   - Estilo E-E-A-T: demuestra experiencia real, no genérica
   - NO inventes datos históricos sin base. Si no tienes certeza, usa frases como "se cree que" o "según la tradición"
   - Separa bien cada palabra. Usa comas, puntos y párrafos correctos.

4. tips_secrets (OBLIGATORIO):
   - Lista de exactamente 6 consejos profesionales específicos para ESTA receta
   - Cada consejo en una línea nueva comenzando con número y punto: "1. Consejo aquí."
   - Incluye el PORQUÉ técnico de cada consejo (ciencia de la cocina, textura, sabor)
   - Ejemplos del estilo esperado:
     "1. Usa ingrediente X porque la reacción química Y produce textura Z."
     "2. Temperatura recomendada: explica el por qué."
   - Separa bien las palabras. Ortografía española correcta.

5. faq (OBLIGATORIO):
   - Exactamente 4 objetos {"question": "...", "answer": "..."}
   - Las preguntas deben ser las que la gente REALMENTE busca en Google sobre este plato
   - Respuestas de 2 a 3 oraciones completas, informativas, con autoridad
   - Las respuestas deben añadir valor SEO con variaciones de keyword naturales

6. amazon_keywords (OBLIGATORIO):
   - Exactamente 5 strings de búsqueda en texto plano, sin comillas internas
   - Deben ser términos reales que alguien usaría en Amazon para encontrar libros de cocina relacionados
   - Ejemplos: "cocina peruana tradicional recetas", "libro recetas latinoamericanas", "gastronomia {$country} libro"

7. internal_link_suggestions (OBLIGATORIO):
   - Exactamente 3 títulos de recetas relacionadas que podrían existir en el blog
   - Texto plano simple: solo el nombre de la receta sugerida, sin caracteres especiales, sin llaves, sin corchetes, sin símbolos
   - Ejemplos correctos: "Ceviche de Camarones", "Tiradito de Salmón", "Leche de Tigre Clásica"
   - Ejemplos INCORRECTOS: "{#}Título{#}", "[Receta]", "Receta: Nombre"

=== RECORDATORIO FINAL ===
- Responde SOLO con el JSON. Ningún texto antes ni después.
- Verifica que cada campo tenga contenido real antes de responder.
- Verifica que las palabras estén bien separadas con espacios.
- El JSON debe ser parseable sin errores.
PROMPT;
    }

    private function validateAndClean(array $data): array
    {
        // Normalize internal_link_suggestions: accept both plain strings and objects
        $rawLinks = array_slice($data['internal_link_suggestions'] ?? [], 0, 3);
        $links = array_values(array_map(function ($item) {
            if (is_string($item)) {
                return $this->cleanText($item);
            }
            // If AI still returns objects, extract the most useful text field
            if (is_array($item)) {
                $text = $item['topic'] ?? $item['anchor_text'] ?? $item['title'] ?? json_encode($item);
                return $this->cleanText($text);
            }
            return $this->cleanText((string) $item);
        }, $rawLinks));

        return [
            'seo_title'       => isset($data['seo_title'])
                ? substr($this->cleanText($this->forceString($data['seo_title'])), 0, 60) : null,
            'seo_description' => isset($data['seo_description'])
                ? substr($this->cleanText($this->forceString($data['seo_description'])), 0, 160) : null,
            'story'           => isset($data['story'])
                ? trim($this->forceString($data['story'])) : null,
            'tips_secrets'    => isset($data['tips_secrets'])
                ? trim($this->forceString($data['tips_secrets'])) : null,
            'faq'             => array_slice($data['faq'] ?? [], 0, 6),
            'amazon_keywords' => array_map(
                fn($k) => $this->cleanText($this->forceString($k)),
                array_slice($data['amazon_keywords'] ?? [], 0, 5)
            ),
            'internal_link_suggestions' => $links,
        ];
    }

    /**
     * Remove stray special markers that small models sometimes inject.
     * Normalise multiple spaces.
     */
    private function cleanText(string $text): string
    {
        // Remove {#...#} or [#...#] style markers
        $text = preg_replace('/\{#[^}]*#?\}|\[#[^\]]*#?\]/', '', $text);
        // Collapse multiple spaces into one
        $text = preg_replace('/\s{2,}/', ' ', $text);
        return trim($text);
    }

    /**
     * Safely convert any AI-returned value to a plain string.
     * Arrays of strings → joined with newline (common for tips/story returned as list).
     * Objects/other → JSON encode as fallback.
     */
    private function forceString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_array($value)) {
            // Flatten: each element may be a string or an object with a text key
            $lines = array_map(function ($item) {
                if (is_string($item)) return $item;
                if (is_array($item)) {
                    return $item['text'] ?? $item['tip'] ?? $item['content'] ?? $item['step'] ?? json_encode($item, JSON_UNESCAPED_UNICODE);
                }
                return (string) $item;
            }, $value);
            return implode("\n", $lines);
        }
        return (string) $value;
    }
}
