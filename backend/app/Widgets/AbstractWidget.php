<?php

namespace App\Widgets;

use App\Widgets\Contracts\Widget;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

/**
 * Base widget. `type()` is derived from the class name (HeroBlock -> "hero",
 * DienstCardsBlock -> "dienst_cards") so it stays in sync with the frontend keys.
 */
abstract class AbstractWidget implements Widget
{
    public static function type(): string
    {
        return Str::of(class_basename(static::class))
            ->beforeLast('Block')
            ->snake()
            ->toString();
    }

    public static function scopes(): array
    {
        return ['page'];
    }

    public function defaultData(): array
    {
        return [];
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public function group(): ?string
    {
        return null;
    }
}
