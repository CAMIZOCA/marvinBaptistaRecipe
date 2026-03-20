<?php

namespace App\Services;

use App\Models\AmazonBook;
use App\Models\Recipe;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecipeEnhancer
{
    public function enhance(Recipe $recipe): array
    {
        $recipe->load('ingredients', 'steps', 'categories', 'tags');

        $prompt   = $this->buildUserPrompt($recipe);
        $provider = Setting::get('ai_provider', 'anthropic');
        $model = $this->resolveProviderModel((string) $provider);

        $rawContent = $this->callProvider((string) $provider, $prompt);

        $content = $this->extractJsonFromResponse($rawContent);

        $result = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback: try to rescue individual fields with regex (handles small-model malformed JSON)
            $result = $this->fallbackExtract($content);
            if (empty($result)) {
                throw new \RuntimeException('La IA devolvió una respuesta inválida (JSON malformado). Intenta con un modelo más capaz o ajusta las instrucciones en Ajustes → IA.');
            }
        }

        $clean = $this->validateAndClean($result);
        $this->logAiExchange('enhance_full', [
            'recipe_id' => $recipe->id,
            'recipe_title' => $recipe->title,
            'provider' => (string) $provider,
            'model' => $model,
            'prompt' => $prompt,
            'raw_response' => $rawContent,
            'normalized_response' => $content,
            'clean_result' => $clean,
        ]);

        return $clean;
    }

    private function callProvider(string $provider, string $prompt): string
    {
        return match ($provider) {
            'anthropic' => $this->callAnthropic($prompt),
            'local'  => $this->callLocalAi($prompt),
            'openai' => $this->callOpenAi($prompt),
            'gemini' => $this->callGemini($prompt),
            'gemma'  => $this->callGemma($prompt),
            'deepinfra' => $this->callDeepinfra($prompt),
            default  => throw new \RuntimeException(
                "Proveedor de IA no válido: '{$provider}'. Ve a Ajustes -> IA, selecciona un proveedor válido y guarda antes de procesar."
            ),
        };
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

        $url = $this->normalizeOpenAiCompatibleUrl($url);

        $systemPrompt = config('ai.system_prompt',
            'Eres un experto consultor de SEO para blogs de cocina. Responde siempre en español con JSON válido.'
        );

        $timeout = (int) Setting::get('local_ai_timeout', 300);

        return $this->callOpenAiCompatible(
            providerLabel: 'IA local',
            endpoint: $url,
            apiKey: (string) ($key ?: 'local'),
            model: (string) $model,
            prompt: $prompt,
            timeout: $timeout,
            systemPrompt: (string) $systemPrompt,
        );
    }

    private function callOpenAi(string $prompt): string
    {
        $apiKey = (string) (Setting::get('openai_api_key') ?: config('services.openai.key'));
        $model = (string) (Setting::get('openai_model') ?: config('ai.openai.model', 'gpt-4.1-mini'));
        $url = (string) config('ai.openai.api_url', config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions'));
        $timeout = (int) config('ai.openai.timeout', config('services.openai.timeout', 90));

        if (blank($apiKey)) {
            throw new \RuntimeException('No hay clave API de OpenAI configurada. Ve a Ajustes â†’ IA y agrega tu clave.');
        }

        return $this->callOpenAiCompatible(
            providerLabel: 'OpenAI',
            endpoint: $this->normalizeOpenAiCompatibleUrl($url),
            apiKey: $apiKey,
            model: $model,
            prompt: $prompt,
            timeout: $timeout,
            systemPrompt: (string) config('ai.system_prompt'),
        );
    }

    private function callGemini(string $prompt): string
    {
        $apiKey = (string) (Setting::get('gemini_api_key') ?: config('services.gemini.key'));
        $model = (string) (Setting::get('gemini_model') ?: config('ai.gemini.model', 'gemini-2.5-flash'));
        $url = (string) config('ai.gemini.api_url', config('services.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'));
        $timeout = (int) config('ai.gemini.timeout', config('services.gemini.timeout', 90));

        if (blank($apiKey)) {
            throw new \RuntimeException('No hay clave API de Gemini configurada. Ve a Ajustes â†’ IA y agrega tu clave.');
        }

        return $this->callOpenAiCompatible(
            providerLabel: 'Gemini',
            endpoint: $this->normalizeOpenAiCompatibleUrl($url),
            apiKey: $apiKey,
            model: $model,
            prompt: $prompt,
            timeout: $timeout,
            systemPrompt: (string) config('ai.system_prompt'),
        );
    }

    private function callGemma(string $prompt): string
    {
        $url = (string) (Setting::get('gemma_api_url') ?: config('ai.gemma.api_url', config('services.gemma.api_url', 'http://localhost:11434/v1/chat/completions')));
        $model = (string) (Setting::get('gemma_model') ?: config('ai.gemma.model', 'gemma3:4b'));
        $key = (string) (Setting::get('gemma_api_key') ?: config('services.gemma.key', 'local'));
        $timeout = (int) (Setting::get('gemma_timeout') ?: config('ai.gemma.timeout', config('services.gemma.timeout', 300)));

        if (blank($url)) {
            throw new \RuntimeException('No hay URL para Gemma configurada. Ve a Ajustes â†’ IA.');
        }

        return $this->callOpenAiCompatible(
            providerLabel: 'Gemma',
            endpoint: $this->normalizeOpenAiCompatibleUrl($url),
            apiKey: $key ?: 'local',
            model: $model,
            prompt: $prompt,
            timeout: $timeout,
            systemPrompt: (string) config('ai.system_prompt'),
        );
    }

    private function callDeepinfra(string $prompt): string
    {
        $apiKey = (string) (Setting::get('deepinfra_api_key') ?: config('services.deepinfra.key'));
        $model = (string) (Setting::get('deepinfra_model') ?: config('ai.deepinfra.model', config('services.deepinfra.model', 'meta-llama/Llama-3.3-70B-Instruct')));
        $url = (string) (Setting::get('deepinfra_api_url') ?: config('ai.deepinfra.api_url', config('services.deepinfra.api_url', 'https://api.deepinfra.com/v1/openai/chat/completions')));
        $timeout = (int) (Setting::get('deepinfra_timeout') ?: config('ai.deepinfra.timeout', config('services.deepinfra.timeout', 180)));

        if (blank($apiKey)) {
            throw new \RuntimeException('No hay clave API de DeepInfra configurada. Ve a Ajustes -> IA y agrega tu clave.');
        }

        return $this->callOpenAiCompatible(
            providerLabel: 'DeepInfra',
            endpoint: $this->normalizeOpenAiCompatibleUrl($url),
            apiKey: $apiKey,
            model: $model,
            prompt: $prompt,
            timeout: $timeout,
            systemPrompt: (string) config('ai.system_prompt'),
        );
    }

    private function normalizeOpenAiCompatibleUrl(string $url): string
    {
        $url = rtrim($url, '/');
        if (str_ends_with($url, '/chat/completions')) {
            return $url;
        }

        // DeepInfra/OpenAI proxy style base (e.g. /v1/openai)
        if (str_ends_with($url, '/openai') || str_ends_with($url, '/v1/openai')) {
            return $url . '/chat/completions';
        }

        $url .= '/v1/chat/completions';

        return $url;
    }

    private function callOpenAiCompatible(
        string $providerLabel,
        string $endpoint,
        string $apiKey,
        string $model,
        string $prompt,
        int $timeout,
        string $systemPrompt
    ): string {
        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . ($apiKey ?: 'local'),
            ])
            ->connectTimeout(15)
            ->timeout($timeout > 0 ? $timeout : 90)
            ->retry(1, 250)
            ->post($endpoint, [
                'model'    => $model,
                'stream'   => false,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                $errorMessage = $response->json('error.message')
                    ?? $response->json('message')
                    ?? $response->body();
                throw new \RuntimeException("Error {$providerLabel} {$response->status()}: {$errorMessage}");
            }

            return (string) $response->json('choices.0.message.content', '');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \RuntimeException(
                "{$providerLabel}: no se pudo conectar o la respuesta tardó demasiado (timeout). Endpoint: {$endpoint}",
                previous: $e
            );
        }
    }

    /* ─── Apply enhancements ──────────────────────────────────────── */

    public function applyEnhancement(Recipe $recipe, array $fields): void
    {
        $allowedFields = ['seo_title', 'seo_description', 'story', 'tips_secrets'];
        $updateData = [];

        // Fields rendered inside a Trix (rich-text) editor — plain-text AI output
        // (with \n line breaks) must be converted to HTML so Trix displays it correctly.
        $richTextFields = ['story', 'tips_secrets'];

        foreach ($allowedFields as $field) {
            if (isset($fields[$field]) && $fields[$field]) {
                $value = $fields[$field];
                if (in_array($field, $richTextFields)) {
                    $value = $this->plainTextToHtml((string) $value);
                }
                $updateData[$field] = $value;
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

    /**
     * Convert plain-text AI output to basic Trix-compatible HTML.
     * - Double newlines  → paragraph breaks  (<div>…</div>)
     * - Single newlines  → line breaks       (<br>)
     * - Already-HTML content is returned as-is (detected by presence of tags).
     */
    private function plainTextToHtml(string $text): string
    {
        $text = trim($text);

        // Already contains HTML tags → Trix can handle it directly
        if ($text !== strip_tags($text)) {
            return $text;
        }

        // Split on two or more consecutive newlines (paragraph breaks)
        $paragraphs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($paragraphs)) {
            return '<div>' . htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</div>';
        }

        $html = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph === '') continue;

            // Escape each line individually, then join with <br>
            $lines   = explode("\n", $paragraph);
            $escaped = implode('<br>', array_map(
                fn (string $l) => htmlspecialchars($l, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                $lines
            ));

            $html .= '<div>' . $escaped . '</div>';
        }

        return $html !== ''
            ? $html
            : '<div>' . htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</div>';
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

    /* ─── Per-field AI call ───────────────────────────────────────── */

    /**
     * Generate a single AI field for a recipe.
     * Each call uses a focused mini-prompt (shorter → faster + fewer JSON errors).
     * Returns the cleaned, ready-to-use value (string, array, etc.).
     */
    public function enhanceSingleField(Recipe $recipe, string $field): mixed
    {
        $recipe->load('ingredients', 'steps', 'categories', 'tags');

        $prompt   = $this->buildFieldPrompt($recipe, $field);
        $provider = Setting::get('ai_provider', 'anthropic');
        $model = $this->resolveProviderModel((string) $provider);

        $rawContent = $this->callProvider((string) $provider, $prompt);
        $content = $this->extractJsonFromResponse($rawContent);

        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            $decoded = $this->fallbackExtract($content);
        }

        if (empty($decoded) || !array_key_exists($field, $decoded)) {
            throw new \RuntimeException(
                "La IA no generó el campo «{$field}». Verifica que el modelo esté disponible e inténtalo de nuevo."
            );
        }

        $cleaned = $this->validateAndClean($decoded);
        $value = $cleaned[$field] ?? null;

        $this->logAiExchange('enhance_field', [
            'recipe_id' => $recipe->id,
            'recipe_title' => $recipe->title,
            'field' => $field,
            'provider' => (string) $provider,
            'model' => $model,
            'prompt' => $prompt,
            'raw_response' => $rawContent,
            'normalized_response' => $content,
            'value' => $value,
        ]);

        return $value;
    }

    private function resolveProviderModel(string $provider): string
    {
        return match ($provider) {
            'local'  => (string) (Setting::get('local_ai_model', 'llama3.2') ?: 'llama3.2'),
            'openai' => (string) (Setting::get('openai_model') ?: config('ai.openai.model', 'gpt-4.1-mini')),
            'gemini' => (string) (Setting::get('gemini_model') ?: config('ai.gemini.model', 'gemini-2.5-flash')),
            'gemma'  => (string) (Setting::get('gemma_model') ?: config('ai.gemma.model', 'gemma3:4b')),
            'deepinfra' => (string) (Setting::get('deepinfra_model') ?: config('ai.deepinfra.model', 'meta-llama/Llama-3.3-70B-Instruct')),
            default  => (string) (Setting::get('anthropic_model') ?: config('ai.anthropic.model', 'claude-sonnet-4-6')),
        };
    }

    private function logAiExchange(string $event, array $payload): void
    {
        try {
            Log::channel('ai')->info($event, $payload);
        } catch (\Throwable $e) {
            Log::info('ai_log_fallback', [
                'event' => $event,
                'payload' => $payload,
                'log_error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build a focused single-field prompt (much shorter than the full prompt,
     * which makes small local models more reliable and faster).
     */
    private function buildFieldPrompt(Recipe $recipe, string $field): string
    {
        $context = $this->buildRecipeContext($recipe);

        $labels = [
            'seo_title'                 => 'el SEO title',
            'seo_description'           => 'la SEO description',
            'story'                     => 'la historia/introducción narrativa',
            'tips_secrets'              => 'los trucos y secretos profesionales',
            'faq'                       => 'las preguntas frecuentes (FAQ)',
            'amazon_keywords'           => 'los keywords de Amazon',
            'internal_link_suggestions' => 'las sugerencias de links internos',
        ];

        $instructions = [
            'seo_title'                 => $this->fieldInstruction('seo_title', <<<'I'
   - Máximo 60 caracteres exactos (cuenta los espacios)
   - Incluye la keyword principal (nombre del plato)
   - Formato: "Receta de [Plato] [Adjetivo o Beneficio]"
   - NO dejes vacío. NO uses null.
I),
            'seo_description'           => $this->fieldInstruction('seo_description', <<<'I'
   - Entre 140 y 155 caracteres exactos (cuenta los espacios)
   - Incluye: keyword principal + beneficio concreto + llamada a la acción
   - NO dejes vacío. NO uses null.
I),
            'story'                     => $this->fieldInstruction('story', <<<'I'
   - Mínimo 400 palabras de narrativa fluida en español
   - Estructura: enganche → origen cultural → evolución → invitación al lector
   - Tono cálido, educativo, en primera persona plural
I),
            'tips_secrets'              => $this->fieldInstruction('tips_secrets', <<<'I'
   - TIPO: string de texto plano (NO {}, NO [], es un string normal)
   - Exactamente 6 consejos separados por \n, formato: "1. Consejo.\n2. ...\n6. ..."
   - Incluye el porqué técnico de cada consejo
I),
            'faq'                       => $this->fieldInstruction('faq', <<<'I'
   - Exactamente 4 preguntas reales que la gente busca en Google sobre este plato
   - Respuestas de 2-3 oraciones informativas con variaciones de keyword
I),
            'amazon_keywords'           => $this->fieldInstruction('amazon_keywords', <<<'I'
   - Exactamente 5 términos de búsqueda en Amazon para libros de cocina relacionados
   - Texto plano sin comillas internas
I),
            'internal_link_suggestions' => $this->fieldInstruction('internal_links', <<<'I'
   - Exactamente 3 títulos de recetas relacionadas que podrían existir en el blog
   - Solo el nombre de la receta, sin caracteres especiales
I),
        ];

        $schemas = [
            'seo_title'                 => '{"seo_title": "El título SEO aquí, máximo 60 caracteres"}',
            'seo_description'           => '{"seo_description": "La meta description aquí, entre 140-155 caracteres"}',
            'story'                     => '{"story": "La historia narrativa larga aquí en un solo string"}',
            'tips_secrets'              => '{"tips_secrets": "1. Consejo uno.\n2. Consejo dos.\n3. Tres.\n4. Cuatro.\n5. Cinco.\n6. Seis."}',
            'faq'                       => "{\n  \"faq\": [\n    {\"question\": \"Pregunta 1\", \"answer\": \"Respuesta 1.\"},\n    {\"question\": \"Pregunta 2\", \"answer\": \"Respuesta 2.\"},\n    {\"question\": \"Pregunta 3\", \"answer\": \"Respuesta 3.\"},\n    {\"question\": \"Pregunta 4\", \"answer\": \"Respuesta 4.\"}\n  ]\n}",
            'amazon_keywords'           => '{"amazon_keywords": ["keyword uno", "keyword dos", "keyword tres", "keyword cuatro", "keyword cinco"]}',
            'internal_link_suggestions' => '{"internal_link_suggestions": ["Nombre Receta 1", "Nombre Receta 2", "Nombre Receta 3"]}',
        ];

        $label       = $labels[$field]       ?? $field;
        $instruction = $instructions[$field] ?? '   - Genera el contenido apropiado para este campo.';
        $schema      = $schemas[$field]      ?? '{"' . $field . '": "valor aquí"}';

        return <<<PROMPT
Genera ÚNICAMENTE {$label} para esta receta de cocina en español.

=== DATOS DE LA RECETA ===
{$context}

=== INSTRUCCIÓN ===
{$instruction}

Responde SOLO con este JSON (nada más, sin texto adicional, sin bloques de código):
{$schema}
PROMPT;
    }

    /* ─── Full prompt (all fields at once) ───────────────────────── */

    public function buildUserPrompt(Recipe $recipe): string
    {
        $context = $this->buildRecipeContext($recipe);

        // Extract individual vars for the heredoc
        $country    = trim($recipe->origin_country ?? 'desconocido');

        // ── Per-field instructions (customisable from Admin → Ajustes → IA) ──
        $instrSeoTitle = $this->fieldInstruction('seo_title', <<<'INSTR'
   - Máximo 60 caracteres exactos, cuenta los espacios
   - Incluye la keyword principal (nombre del plato)
   - Formato preferido: "Receta de [Plato] [Adjetivo o Beneficio]"
   - Ejemplo correcto: "Receta de Ceviche Peruano Auténtico: Fácil y Refrescante"
   - NO uses null. NO dejes vacío.
INSTR);

        $instrSeoDesc = $this->fieldInstruction('seo_description', <<<INSTR
   - Entre 140 y 155 caracteres exactos, cuenta los espacios
   - Incluye: keyword principal + beneficio concreto + llamada a la acción
   - Ejemplo correcto: "Aprende a preparar {$recipe->title} con esta receta tradicional de {$country}. Ingredientes sencillos, técnica de chef y sabor auténtico garantizado."
   - NO uses null. NO dejes vacío. CUENTA los caracteres antes de responder.
INSTR);

        $instrStory = $this->fieldInstruction('story', <<<INSTR
   - Mínimo 500 palabras, máximo 650 palabras
   - Escrito en primera persona plural como chef con experiencia en {$country}
   - Estructura: párrafo de enganche → origen histórico/cultural documentable → evolución del plato → significado en la gastronomía local → cómo lo descubrió el autor → invitación al lector
   - Debe ser factualmente correcto: menciona regiones reales, épocas históricas aproximadas, técnicas tradicionales verificables
   - Tono: cálido, apasionado, educativo; apto para alguien que prueba este plato por primera vez
   - Estilo E-E-A-T: demuestra experiencia real, no genérica
   - NO inventes datos históricos sin base. Si no tienes certeza, usa frases como "se cree que" o "según la tradición"
   - Separa bien cada palabra. Usa comas, puntos y párrafos correctos.
INSTR);

        $instrTips = $this->fieldInstruction('tips_secrets', <<<'INSTR'
   - TIPO: string de texto plano (NO uses {}, NO uses [], es un string normal)
   - Escribe exactamente 6 consejos separados por \n, cada uno comenzando con "N. "
   - Formato CORRECTO: "1. Consejo uno.\n2. Consejo dos.\n3. ...\n4. ...\n5. ...\n6. ..."
   - Formato INCORRECTO: {"1": "consejo"} o ["consejo1", "consejo2"]
   - Incluye el PORQUÉ técnico de cada consejo (ciencia de la cocina, textura, sabor)
   - Separa bien las palabras. Ortografía española correcta.
INSTR);

        $instrFaq = $this->fieldInstruction('faq', <<<'INSTR'
   - Exactamente 4 objetos {"question": "...", "answer": "..."}
   - Las preguntas deben ser las que la gente REALMENTE busca en Google sobre este plato
   - Respuestas de 2 a 3 oraciones completas, informativas, con autoridad
   - Las respuestas deben añadir valor SEO con variaciones de keyword naturales
INSTR);

        $instrAmazon = $this->fieldInstruction('amazon_keywords', <<<INSTR
   - Exactamente 5 strings de búsqueda en texto plano, sin comillas internas
   - Deben ser términos reales que alguien usaría en Amazon para encontrar libros de cocina relacionados
   - Ejemplos: "cocina peruana tradicional recetas", "libro recetas latinoamericanas", "gastronomia {$country} libro"
INSTR);

        $instrLinks = $this->fieldInstruction('internal_links', <<<'INSTR'
   - Exactamente 3 títulos de recetas relacionadas que podrían existir en el blog
   - Texto plano simple: solo el nombre de la receta sugerida, sin caracteres especiales, sin llaves, sin corchetes, sin símbolos
   - Ejemplos correctos: "Ceviche de Camarones", "Tiradito de Salmón", "Leche de Tigre Clásica"
   - Ejemplos INCORRECTOS: "{#}Título{#}", "[Receta]", "Receta: Nombre"
INSTR);

        return <<<PROMPT
Analiza esta receta y genera contenido SEO de máxima calidad en español correcto y fluido.

=== DATOS DE LA RECETA ===
{$context}

=== INSTRUCCIONES POR CAMPO ===

1. seo_title (OBLIGATORIO):
{$instrSeoTitle}

2. seo_description (OBLIGATORIO):
{$instrSeoDesc}

3. story (OBLIGATORIO):
{$instrStory}

4. tips_secrets (OBLIGATORIO):
{$instrTips}

5. faq (OBLIGATORIO):
{$instrFaq}

6. amazon_keywords (OBLIGATORIO):
{$instrAmazon}

7. internal_link_suggestions (OBLIGATORIO):
{$instrLinks}

=== ESTRUCTURA JSON EXACTA ===
Responde ÚNICAMENTE con este JSON (reemplaza los valores, no cambies las claves ni la estructura):

{
  "seo_title": "Texto de máximo 60 caracteres",
  "seo_description": "Texto de entre 140 y 155 caracteres con keyword + beneficio + CTA",
  "story": "Texto narrativo largo en un SOLO string de texto plano",
  "tips_secrets": "1. Primer consejo con porqué técnico.\n2. Segundo consejo.\n3. Tercer consejo.\n4. Cuarto consejo.\n5. Quinto consejo.\n6. Sexto consejo.",
  "faq": [
    {"question": "Pregunta 1", "answer": "Respuesta informativa 1."},
    {"question": "Pregunta 2", "answer": "Respuesta informativa 2."},
    {"question": "Pregunta 3", "answer": "Respuesta informativa 3."},
    {"question": "Pregunta 4", "answer": "Respuesta informativa 4."}
  ],
  "amazon_keywords": ["keyword1", "keyword2", "keyword3", "keyword4", "keyword5"],
  "internal_link_suggestions": ["Nombre Receta 1", "Nombre Receta 2", "Nombre Receta 3"]
}

CRÍTICO: tips_secrets es un STRING con \n entre consejos, NUNCA un objeto {} ni un array [].
CRÍTICO: Devuelve SOLO el JSON. Sin texto antes, sin texto después, sin bloques de código.
PROMPT;
    }

    /* ─── Recipe context builder (shared by full & per-field prompts) */

    private function buildRecipeContext(Recipe $recipe): string
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

        return <<<CTX
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
CTX;
    }

    /* ─── JSON extraction (handles reasoning-model output) ───────── */

    /**
     * Strip <think> blocks (DeepSeek/QwQ reasoning models) and extract the
     * first valid JSON object from the AI response, regardless of whether it
     * is wrapped in a markdown code fence or not.
     */
    private function extractJsonFromResponse(string $content): string
    {
        // 1. Remove <think>...</think> reasoning blocks produced by DeepSeek, QwQ, Qwen-thinking…
        $content = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $content);

        // 2. Try to extract from markdown code fences: ```json … ``` or ``` … ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/s', $content, $matches)) {
            $candidate = trim($matches[1]);
            if (str_starts_with($candidate, '{')) {
                return $candidate;
            }
        }

        // 3. Find the outermost JSON object by scanning for first { and last }
        $start = strpos($content, '{');
        $end   = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return trim(substr($content, $start, $end - $start + 1));
        }

        return trim($content);
    }

    /* ─── Fallback field extraction (regex rescue for malformed JSON) ─ */

    /**
     * When json_decode fails entirely (common with small local models that
     * generate structurally invalid JSON), attempt to extract individual
     * field values using targeted regex patterns so we can still return
     * partial results to the user.
     */
    private function fallbackExtract(string $raw): array
    {
        $result = [];

        // ── Simple / long string fields ───────────────────────────────
        foreach (['seo_title', 'seo_description', 'story'] as $field) {
            if (preg_match('/"' . $field . '"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $raw, $m)) {
                $result[$field] = stripcslashes($m[1]);
            }
        }

        // ── tips_secrets: string, OR malformed object/array fallback ──
        if (preg_match('/"tips_secrets"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $raw, $m)) {
            $result['tips_secrets'] = stripcslashes($m[1]);
        } elseif (preg_match('/"tips_secrets"\s*:\s*[\[{](.*?)[\]}]/s', $raw, $m)) {
            // Model generated {} or [] instead of a string — extract all quoted values
            preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"/s', $m[1], $items);
            $lines = array_values(array_filter($items[1], fn($s) => mb_strlen($s) > 3));
            if ($lines) {
                $result['tips_secrets'] = implode("\n", array_map(
                    fn($i, $v) => (($i + 1) . '. ' . ltrim(preg_replace('/^\d+[\.\)]\s*/', '', $v))),
                    array_keys($lines), $lines
                ));
            }
        }

        // ── faq: try json_decode on just the array literal ────────────
        if (preg_match('/"faq"\s*:\s*(\[[\s\S]*?\])\s*[,}]/s', $raw, $m)) {
            $faq = json_decode($m[1], true);
            if (is_array($faq)) {
                $result['faq'] = $faq;
            }
        }

        // ── amazon_keywords: array of strings ────────────────────────
        if (preg_match('/"amazon_keywords"\s*:\s*(\[[\s\S]*?\])\s*[,}]/s', $raw, $m)) {
            $kw = json_decode($m[1], true);
            if (is_array($kw)) {
                $result['amazon_keywords'] = $kw;
            } else {
                preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"/s', $m[1], $items);
                $result['amazon_keywords'] = array_values(array_filter($items[1], fn($s) => mb_strlen($s) > 1));
            }
        }

        // ── internal_link_suggestions ─────────────────────────────────
        if (preg_match('/"internal_link_suggestions"\s*:\s*(\[[\s\S]*?\])\s*[,}]/s', $raw, $m)) {
            $links = json_decode($m[1], true);
            if (is_array($links)) {
                $result['internal_link_suggestions'] = $links;
            } else {
                preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"/s', $m[1], $items);
                $result['internal_link_suggestions'] = array_values(array_filter($items[1], fn($s) => mb_strlen($s) > 1));
            }
        }

        return $result;
    }

    /* ─── Per-field prompt helper ─────────────────────────────────── */

    /**
     * Returns a custom per-field instruction from the DB settings, falling back
     * to the provided default when the setting is empty or not set.
     */
    private function fieldInstruction(string $field, string $default): string
    {
        $custom = Setting::get('ai_prompt_' . $field, '');
        return trim($custom) ?: $default;
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



