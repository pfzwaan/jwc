<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CMS Page — composed of draggable blocks (PageBlock) and, together with its
 * siblings, drives the site header menu (tree via parent_id + menu fields).
 * Dutch-only; no locale layer.
 */
#[Fillable([
    'name', 'title', 'slug', 'parent_id', 'is_published',
    'menu_visibility', 'menu_sort', 'menu_dropdown_type',
    'menu_dropdown_cta', 'menu_featured_card', 'menu_link', 'menu_diensten', 'seo',
])]
class Page extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'menu_visibility' => 'boolean',
            'menu_diensten' => 'boolean',
            'menu_sort' => 'integer',
            'menu_dropdown_cta' => 'array',
            'menu_featured_card' => 'array',
            'seo' => 'array',
        ];
    }

    /** Blocks in render order. */
    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }

    public function visibleBlocks(): HasMany
    {
        return $this->blocks()->where('is_visible', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Child pages in menu order — these become dropdown/mega-menu items. */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('menu_sort');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Visible in the header menu. */
    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('menu_visibility', true)->where('is_published', true);
    }

    /** Top-level pages (no parent). */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Full URL path, walking the parent chain: '/diensten/webdesign'. */
    public function fullPath(): string
    {
        $segments = [];
        for ($page = $this; $page !== null; $page = $page->parent) {
            $slug = trim((string) $page->slug, '/');
            if ($slug !== '' && $slug !== 'home') {
                array_unshift($segments, $slug);
            }
        }

        return '/'.implode('/', $segments);
    }

    /** Href used in the menu: the explicit override if set, else the slug path. */
    public function menuHref(): string
    {
        $link = trim((string) $this->menu_link);

        return $link !== '' ? $link : $this->fullPath();
    }

    /** Depth in the tree (0 = root) — used for table indentation. */
    public function depth(): int
    {
        $depth = 0;
        for ($page = $this->parent; $page !== null; $page = $page->parent) {
            $depth++;
        }

        return $depth;
    }
}
