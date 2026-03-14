<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_books', function (Blueprint $table) {
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('amazon_books')->cascadeOnDelete();
            $table->string('relevance_type', 50)->nullable(); // technique, regional, same_recipe
            $table->primary(['recipe_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_books');
    }
};
