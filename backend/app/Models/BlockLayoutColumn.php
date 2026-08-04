<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['block_layout_id', 'sort_order', 'is_visible'])]
class BlockLayoutColumn extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(BlockLayout::class, 'block_layout_id');
    }

    /** Blocks in render order. */
    public function blocks(): HasMany
    {
        return $this->hasMany(BlockLayoutBlock::class)->orderBy('sort_order');
    }
}
