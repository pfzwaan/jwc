<?php

namespace App\Models;

use App\Widgets\Contracts\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A single widget instance within a BlockLayoutColumn. `type` matches a WidgetRegistry entry. */
#[Fillable(['block_layout_column_id', 'type', 'data', 'sort_order', 'is_visible'])]
class BlockLayoutBlock extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(BlockLayoutColumn::class, 'block_layout_column_id');
    }

    public function widget(): ?Widget
    {
        return app(WidgetRegistry::class)->find($this->type);
    }
}
