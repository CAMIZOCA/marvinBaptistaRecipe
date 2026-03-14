<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order_position')->default(0);
            $table->decimal('amount', 8, 3)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('ingredient_name');
            $table->string('ingredient_group')->nullable(); // e.g. "Para la masa"
            $table->string('notes')->nullable(); // e.g. "tamizado"
            $table->timestamps();

            $table->index(['recipe_id', 'order_position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
