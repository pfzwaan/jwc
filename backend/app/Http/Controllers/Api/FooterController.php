<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockLayout;
use App\Models\BlockLayoutColumn;
use Illuminate\Http\JsonResponse;

/**
 * The editable footer columns (Filament: "Footer" page). Visible columns only,
 * each with its visible widget blocks in order.
 */
class FooterController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $layout = BlockLayout::query()->where('key', 'footer')->where('is_active', true)->first();

        $columns = $layout
            ? $layout->columns->where('is_visible', true)->map(fn (BlockLayoutColumn $column) => [
                'id' => $column->id,
                'blocks' => $column->blocks->where('is_visible', true)->map(fn ($block) => [
                    'type' => $block->type,
                    'data' => $block->data,
                ])->values()->all(),
            ])->values()->all()
            : [];

        return response()->json(['data' => ['columns' => $columns]]);
    }
}
