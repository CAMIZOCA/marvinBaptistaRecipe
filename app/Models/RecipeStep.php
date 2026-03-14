<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeStep extends Model
{
    protected $fillable = [
        'recipe_id', 'step_number', 'title', 'description',
        'duration_minutes', 'image',
    ];

    protected $casts = [
        'step_number' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
