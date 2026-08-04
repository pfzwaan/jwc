<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom post type: Project. Its own Filament resource with its own entry in
 * the admin navigation, exposed via /api/v1/projecten.
 */
#[Fillable([
    'title', 'slug', 'image', 'image_mobile', 'url', 'accent_color', 'is_featured',
    'excerpt', 'categories', 'skills', 'body', 'gallery', 'is_published', 'sort_order', 'seo',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'categories' => 'array',
            'skills' => 'array',
            'seo' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Admin order: explicit sort first, newest as tie-breaker. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    /** Shape used by the projects widget and the overview page. */
    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'href' => '/projecten/'.$this->slug,
            'image' => MediaUrl::resolve($this->image),
            // Deliberately no fallback to the desktop shot: a wide screenshot
            // cropped into a portrait phone frame shows a meaningless slice of
            // the site. Empty means "don't render the phone".
            'imageMobile' => MediaUrl::resolve($this->image_mobile),
            'url' => $this->url,
            'categories' => $this->tags($this->categories),
            'skills' => $this->tags($this->skills),
            'accent' => $this->accent_color ?: '#1e62d4',
            'featured' => $this->is_featured,
            'excerpt' => $this->excerpt,
        ];
    }

    /**
     * Repeater rows arrive as [['label' => 'PHP'], …]; flatten to plain strings
     * so the frontend can render them straight as pills.
     *
     * @param  array<int, mixed>|null  $values
     * @return array<int, string>
     */
    private function tags(?array $values): array
    {
        return array_values(array_filter(array_map(
            fn ($v): string => trim(is_array($v) ? (string) ($v['label'] ?? '') : (string) $v),
            $values ?? [],
        )));
    }
}
