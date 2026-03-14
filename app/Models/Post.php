<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'content', 'featured_image', 'image_alt',
        'category', 'seo_title', 'seo_description',
        'is_published', 'published_at', 'view_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /* ─── Scopes ───────────────────────────────────────────── */

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)
                 ->where('published_at', '<=', now());
    }

    /* ─── Accessors ─────────────────────────────────────────── */

    /** First 160 chars of content as fallback when excerpt is empty */
    public function getShortExcerptAttribute(): string
    {
        if ($this->excerpt) {
            return Str::limit($this->excerpt, 160);
        }
        return Str::limit(strip_tags($this->content ?? ''), 160);
    }

    /* ─── Route model binding ───────────────────────────────── */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
