<?php

namespace App\Widgets\Contracts;

use Filament\Support\Icons\Heroicon;

/**
 * A reusable page widget. Each widget is a self-contained class: its Filament
 * form, default data, label and icon. The WidgetRegistry collects them; the
 * page builder renders them as draggable blocks. The frontend BlockRenderer
 * maps `type()` to a React component.
 */
interface Widget
{
    public static function type(): string;

    /** @return array<int, string> */
    public static function scopes(): array;

    public function label(): string;

    public function icon(): string|Heroicon;

    /** @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field> */
    public function form(): array;

    /** @return array<string, mixed> */
    public function defaultData(): array;
}
