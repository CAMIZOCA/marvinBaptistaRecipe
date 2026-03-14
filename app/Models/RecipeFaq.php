<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeFaq extends Model
{
    protected $fillable = ['recipe_id', 'question', 'answer', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
