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

        $prompt = $this->buildUserPrompt($recipe);

        $apiKey = Setting::get('anthropic_api_key') ?: config('services.anthropic.key');
        $model  = Setting::get('anthropic_model')   ?: config('ai.anthropic.model');

        if (blank($apiKey)) {
            throw new \RuntimeException(
                'No hay clave API de IA configurada. Ve a Ajustes → IA y agrega tu clave de Anthropic.'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => config('ai.anthropic.version', '2023-06-01'),
            'content-type'      => 'application/json',
        ])
        ->timeout(config('ai.anthropic.timeout', 90))
        ->post(config('ai.anthropic.api_url'), [
            'model' => $model,
            'max_tokens' => config('ai.anthropic.max_tokens', 4096),
            'system' => config('ai.system_prompt'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Error al conectar con la API de IA: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $data = $response->json();
        $content = $data['content'][0]['text'] ?? '';

        // Extract JSON from response (Claude may wrap in markdown code blocks)
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = $matches[1];
        }

        $result = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('La IA devolvió una respuesta inválida. Por favor intenta nuevamente.');
        }

        return $this->validateAndClean($result);
    }

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
                    'question' => $faqItem['question'] ?? '',
                    'answer' => $faqItem['answer'] ?? '',
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

        // Sync without detaching manually matched ones
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
            if ($ing->unit) $parts[] = $ing->unit;
            $parts[] = $ing->ingredient_name;
            return implode(' ', $parts);
        })->implode(', ');

        $steps = $recipe->steps->map(function ($step) {
            return 'Paso ' . $step->step_number . ': ' . strip_tags($step->description);
        })->implode("\n");

        $category = $recipe->categories->first()?->name ?? 'sin categoría';

        return "Mejora esta receta para máximo SEO y calidad de contenido:\n\n" .
            "TÍTULO ACTUAL: {$recipe->title}\n" .
            "PAÍS DE ORIGEN: {$recipe->origin_country}\n" .
            "CATEGORÍA: {$category}\n" .
            "DESCRIPCIÓN ACTUAL: " . strip_tags($recipe->description ?? '') . "\n" .
            "INGREDIENTES: {$ingredients}\n\n" .
            "PASOS:\n{$steps}\n\n" .
            "TAREAS:\n" .
            "1. seo_title: Crea un título SEO de máximo 60 caracteres con la keyword principal (formato: 'Receta de X: [beneficio]')\n" .
            "2. seo_description: Meta description de 140-155 caracteres con keyword + beneficio + CTA\n" .
            "3. story: Escribe 250-400 palabras sobre el origen cultural/histórico de esta receta en {$recipe->origin_country}. Voz amigable.\n" .
            "4. tips_secrets: Lista de 5-7 secretos profesionales específicos para esta receta con el porqué de cada uno.\n" .
            "5. faq: Genera 4 preguntas frecuentes reales que la gente busca sobre esta receta con respuestas de 2-3 oraciones.\n" .
            "6. amazon_keywords: Genera 5 strings de búsqueda de Amazon para libros de cocina relevantes.\n" .
            "7. internal_link_suggestions: Sugiere 3 temas de otras recetas para linkear internamente.\n\n" .
            "Responde ÚNICAMENTE con el objeto JSON, sin texto adicional.";
    }

    private function validateAndClean(array $data): array
    {
        return [
            'seo_title' => isset($data['seo_title']) ? substr(trim($data['seo_title']), 0, 60) : null,
            'seo_description' => isset($data['seo_description']) ? substr(trim($data['seo_description']), 0, 160) : null,
            'story' => $data['story'] ?? null,
            'tips_secrets' => $data['tips_secrets'] ?? null,
            'faq' => array_slice($data['faq'] ?? [], 0, 6),
            'amazon_keywords' => array_slice($data['amazon_keywords'] ?? [], 0, 5),
            'internal_link_suggestions' => array_slice($data['internal_link_suggestions'] ?? [], 0, 3),
        ];
    }
}
