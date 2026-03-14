<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class AmazonBook extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'asin', 'slug', 'title', 'author', 'cover_image_url',
        'amazon_url_us', 'amazon_url_mx', 'amazon_url_es', 'amazon_url_ar',
        'cuisine_type', 'description', 'keywords_match', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'keywords_match' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_books', 'book_id', 'recipe_id')
            ->withPivot('relevance_type');
    }

    public function getAffiliateUrl(string $countryCode = 'US', string $affiliateTag = ''): string
    {
        $tag = $affiliateTag ?: config('services.amazon.affiliate_tag', '');

        $baseUrl = match(strtoupper($countryCode)) {
            'MX', 'GT', 'HN', 'SV', 'NI', 'CR', 'PA', 'CO', 'VE', 'PE', 'BO', 'CL', 'PY', 'UY', 'EC' =>
                $this->amazon_url_mx ?: 'https://www.amazon.com.mx/dp/' . $this->asin,
            'ES' =>
                $this->amazon_url_es ?: 'https://www.amazon.es/dp/' . $this->asin,
            'AR' =>
                $this->amazon_url_ar ?: 'https://www.amazon.com.ar/dp/' . $this->asin,
            default =>
                $this->amazon_url_us ?: 'https://www.amazon.com/dp/' . $this->asin,
        };

        return $baseUrl . ($tag ? '?tag=' . $tag : '');
    }
}
