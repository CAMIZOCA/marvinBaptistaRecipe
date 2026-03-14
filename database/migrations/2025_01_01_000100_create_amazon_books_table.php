<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amazon_books', function (Blueprint $table) {
            $table->id();
            $table->string('asin', 20)->unique();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('amazon_url_us')->nullable();
            $table->string('amazon_url_mx')->nullable();
            $table->string('amazon_url_es')->nullable();
            $table->string('amazon_url_ar')->nullable();
            $table->string('cuisine_type')->nullable(); // e.g. "ecuatoriana", "mexicana"
            $table->text('description')->nullable();
            $table->json('keywords_match')->nullable(); // array of strings for auto-matching
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('cuisine_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amazon_books');
    }
};
