<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    protected $fillable = [
        'recipe_id', 'order_position', 'amount', 'unit',
        'ingredient_name', 'ingredient_group', 'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'order_position' => 'integer',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        if (!$this->amount) return '';

        // Convert decimal to fraction for display: 0.5 → 1/2, 0.25 → 1/4, etc.
        $fractions = [
            0.125 => '⅛', 0.25 => '¼', 0.333 => '⅓',
            0.5 => '½', 0.667 => '⅔', 0.75 => '¾',
        ];

        $whole = floor($this->amount);
        $decimal = $this->amount - $whole;

        foreach ($fractions as $value => $symbol) {
            if (abs($decimal - $value) < 0.01) {
                return ($whole > 0 ? $whole . ' ' : '') . $symbol;
            }
        }

        return $this->amount == intval($this->amount)
            ? (string) intval($this->amount)
            : (string) $this->amount;
    }
}
