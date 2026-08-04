<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom post type: Blog. Its own Filament resource with its own entry in the
 * admin navigation, exposed via /api/v1/blog.
 */
#[Fillable([
    'title', 'slug', 'excerpt', 'body', 'image',
    'author', 'category', 'is_published', 'published_at', 'seo',
])]
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'seo' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Newest first — posts without a date fall back to creation order. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
    }

    /** Shape used by listing widgets and overview pages. */
    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'href' => '/blog/'.$this->slug,
            'excerpt' => $this->excerpt,
            'image' => MediaUrl::resolve($this->image),
            'author' => $this->author,
            'category' => $this->category,
            'publishedAt' => optional($this->published_at ?? $this->created_at)->toIso8601String(),
            'readMinutes' => $this->readMinutes(),
        ];
    }

    /**
     * Reading time in whole minutes, derived from the body at 200 wpm.
     *
     * Computed rather than a field: it would otherwise be one more thing for
     * staff to keep in sync every time they edit an article.
     */
    public function readMinutes(): int
    {
        // Replace tags with a space rather than stripping them: "</h2><p>" would
        // otherwise glue the last and first word together and undercount.
        $plain = preg_replace('/<[^>]+>/', ' ', (string) $this->body.' '.(string) $this->excerpt);
        $words = count(preg_split('/\s+/', trim((string) $plain), -1, PREG_SPLIT_NO_EMPTY) ?: []);

        return max(1, (int) ceil($words / 200));
    }
}
