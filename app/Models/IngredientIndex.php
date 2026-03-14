<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class IngredientIndex extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'ingredients_index';

    protected $fillable = [
        'name', 'slug', 'description', 'seo_title', 'seo_description', 'image',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
