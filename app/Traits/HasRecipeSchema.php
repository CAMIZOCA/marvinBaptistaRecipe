<?php

namespace App\Traits;

trait HasRecipeSchema
{
    public function toRecipeSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $this->seo_title ?? $this->title,
            'description' => $this->seo_description ?? strip_tags($this->description ?? ''),
            'image' => $this->featured_image ? [asset($this->featured_image)] : [],
            'author' => [
                '@type' => 'Person',
                'name' => config('app.author_name', 'Marvin Baptista'),
            ],
            'datePublished' => $this->published_at?->toIso8601String(),
            'keywords' => $this->seo_keywords,
            'recipeYield' => ($this->servings ?? '') . ' ' . ($this->servings_unit ?? 'porciones'),
            'recipeCategory' => $this->categories->first()?->name ?? '',
            'recipeCuisine' => $this->origin_country ?? '',
        ];

        if ($this->prep_time_minutes) {
            $schema['prepTime'] = 'PT' . $this->prep_time_minutes . 'M';
        }
        if ($this->cook_time_minutes) {
            $schema['cookTime'] = 'PT' . $this->cook_time_minutes . 'M';
        }
        $total = ($this->prep_time_minutes ?? 0) + ($this->cook_time_minutes ?? 0) + ($this->rest_time_minutes ?? 0);
        if ($total > 0) {
            $schema['totalTime'] = 'PT' . $total . 'M';
        }

        // Ingredients
        $schema['recipeIngredient'] = $this->ingredients->map(function ($ingredient) {
            $parts = [];
            if ($ingredient->amount) $parts[] = $ingredient->amount;
            if ($ingredient->unit) $parts[] = $ingredient->unit;
            $parts[] = $ingredient->ingredient_name;
            if ($ingredient->notes) $parts[] = '(' . $ingredient->notes . ')';
            return implode(' ', $parts);
        })->toArray();

        // Instructions
        $schema['recipeInstructions'] = $this->steps->map(function ($step) {
            return [
                '@type' => 'HowToStep',
                'name' => $step->title ?? 'Paso ' . $step->step_number,
                'text' => strip_tags($step->description),
            ];
        })->toArray();

        // Rating
        if ($this->schema_rating_value && $this->schema_rating_count) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $this->schema_rating_value,
                'ratingCount' => (string) $this->schema_rating_count,
                'bestRating' => '5',
                'worstRating' => '1',
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function toFaqSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $this->faqs->map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq->question,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq->answer,
                    ],
                ];
            })->toArray(),
        ];

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function toBreadcrumbSchema(string $baseUrl): string
    {
        $items = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => $baseUrl],
        ];

        $position = 2;
        $primaryCategory = $this->categories->first();
        if ($primaryCategory) {
            if ($primaryCategory->parent) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $primaryCategory->parent->name,
                    'item' => $baseUrl . '/recetas/' . $primaryCategory->parent->slug,
                ];
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $primaryCategory->name,
                'item' => $baseUrl . '/recetas/' . $primaryCategory->slug,
            ];
        }

        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $this->title,
            'item' => $baseUrl . '/' . $this->slug,
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];

        return json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
