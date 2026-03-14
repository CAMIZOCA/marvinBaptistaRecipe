<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('origin_country')->nullable();
            $table->string('origin_region')->nullable();

            // Timing
            $table->unsignedSmallInteger('prep_time_minutes')->nullable();
            $table->unsignedSmallInteger('cook_time_minutes')->nullable();
            $table->unsignedSmallInteger('rest_time_minutes')->nullable();

            // Servings
            $table->unsignedSmallInteger('servings')->nullable();
            $table->string('servings_unit', 50)->default('porciones');

            // Metadata
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('featured_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('video_url')->nullable();

            // Rich content
            $table->longText('story')->nullable();
            $table->longText('tips_secrets')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            // Schema.org
            $table->decimal('schema_rating_value', 2, 1)->nullable();
            $table->unsignedInteger('schema_rating_count')->nullable();

            // Publication
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);

            // Stats & AI
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('ai_enhanced_at')->nullable();

            // Author
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('is_published');
            $table->index('published_at');
            $table->index('origin_country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
