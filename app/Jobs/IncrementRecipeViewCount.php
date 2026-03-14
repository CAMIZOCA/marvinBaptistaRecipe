<?php

namespace App\Jobs;

use App\Models\Recipe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IncrementRecipeViewCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $recipeId) {}

    public function handle(): void
    {
        Recipe::where('id', $this->recipeId)->increment('view_count');
    }
}
