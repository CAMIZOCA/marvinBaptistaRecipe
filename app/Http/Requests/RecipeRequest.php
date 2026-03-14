<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isEditor();
    }

    public function rules(): array
    {
        $recipeId = $this->route('recipe')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'origin_country' => ['nullable', 'string', 'max:100'],
            'origin_region' => ['nullable', 'string', 'max:100'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'cook_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'rest_time_minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'servings' => ['nullable', 'integer', 'min:1'],
            'servings_unit' => ['nullable', 'string', 'max:50'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'story' => ['nullable', 'string'],
            'tips_secrets' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:60'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'schema_rating_value' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'schema_rating_count' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'primary_category' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'ingredients' => ['nullable', 'string'], // JSON string
            'steps' => ['nullable', 'string'], // JSON string
            'faqs' => ['nullable', 'string'], // JSON string
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título de la receta es obligatorio.',
            'difficulty.required' => 'Debes seleccionar un nivel de dificultad.',
            'difficulty.in' => 'El nivel de dificultad debe ser: fácil, media o difícil.',
            'video_url.url' => 'El enlace del video debe ser una URL válida.',
            'seo_title.max' => 'El título SEO no debe superar los 60 caracteres.',
            'seo_description.max' => 'La descripción SEO no debe superar los 160 caracteres.',
        ];
    }
}
