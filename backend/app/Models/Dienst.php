<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom post type: Dienst (service). Its own Filament resource with its own
 * entry in the admin navigation, exposed via /api/v1/diensten.
 */
#[Fillable([
    'title', 'slug', 'label', 'excerpt', 'body', 'image', 'icon',
    'accent_color', 'accent_label_color',
    'features', 'cta_label', 'cta_href', 'is_published', 'sort_order', 'seo',
])]
class Dienst extends Model
{
    protected $table = 'diensten';

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'seo' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Admin/menu order: explicit sort first, newest as tie-breaker. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    /** Shape used by listing widgets and overview pages. */
    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'href' => '/diensten/'.$this->slug,
            'label' => $this->label,
            'excerpt' => $this->excerpt,
            'image' => MediaUrl::resolve($this->image),
            'icon' => $this->icon,
            // Fall back to the brand colour so a dienst without an accent still
            // renders a complete box.
            'accent' => $this->accent_color ?: '#fcb040',
            'accentLabel' => $this->accent_label_color ?: ($this->accent_color ?: '#fcb040'),
            'features' => array_values(array_map(
                fn ($f): string => is_array($f) ? (string) ($f['label'] ?? '') : (string) $f,
                (array) ($this->features ?? []),
            )),
            'ctaLabel' => $this->cta_label,
            'ctaHref' => $this->cta_href,
        ];
    }
}
