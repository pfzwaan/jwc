<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A named, widget-driven layout region (currently just "footer"). Reuses the
 * same Widget/WidgetRegistry contract as the page builder, scoped per-region
 * via Widget::scopes().
 */
#[Fillable(['key', 'name', 'is_active'])]
class BlockLayout extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** Columns in render order. */
    public function columns(): HasMany
    {
        return $this->hasMany(BlockLayoutColumn::class)->orderBy('sort_order');
    }
}
