<?php

namespace App\Models;

use App\Traits\HasRecipeSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Recipe extends Model
{
    use HasFactory, HasSlug, HasRecipeSchema, SoftDeletes;

    protected $fillable = [
        'slug', 'title', 'subtitle', 'description',
        'origin_country', 'origin_region',
        'prep_time_minutes', 'cook_time_minutes', 'rest_time_minutes',
        'servings', 'servings_unit', 'difficulty',
        'featured_image', 'image_alt', 'video_url',
        'story', 'tips_secrets',
        'seo_title', 'seo_description', 'seo_keywords',
        'published_at', 'is_published', 'view_count', 'ai_enhanced_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'ai_enhanced_at' => 'datetime',
        'is_published' => 'boolean',
        'prep_time_minutes' => 'integer',
        'cook_time_minutes' => 'integer',
        'rest_time_minutes' => 'integer',
        'servings' => 'integer',
        'view_count' => 'integer',
        'schema_rating_value' => 'float',
        'schema_rating_count' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Accessors
    public function getTotalTimeMinutesAttribute(): int
    {
        return ($this->prep_time_minutes ?? 0) + ($this->cook_time_minutes ?? 0) + ($this->rest_time_minutes ?? 0);
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => 'Fácil',
            'medium' => 'Media',
            'hard' => 'Difícil',
            default => 'Media',
        };
    }

    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'red',
            default => 'yellow',
        };
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('origin_country', $country);
    }

    public function scopeAiPending($query)
    {
        return $query->whereNull('ai_enhanced_at');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'recipe_category')
            ->withPivot('is_primary');
    }

    public function primaryCategory()
    {
        return $this->categories()->wherePivot('is_primary', true)->first()
            ?? $this->categories()->first();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tag');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('order_position');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(RecipeFaq::class)->orderBy('sort_order');
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(AmazonBook::class, 'recipe_books', 'recipe_id', 'book_id')
            ->withPivot('relevance_type');
    }
}
