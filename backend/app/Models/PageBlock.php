<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single block on a Page. `type` matches a frontend widget in BlockRenderer;
 * `data` holds that widget's content. Reorderable via `sort_order`.
 */
#[Fillable(['page_id', 'type', 'data', 'sort_order', 'is_visible'])]
class PageBlock extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
