<?php

namespace App\Widgets;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * Footer link column — a heading plus a list of label/url links. `href` is a
 * plain TextInput (no ->url() rule): link fields must accept relative paths
 * like "/over-ons" and anchors, not just absolute URLs.
 */
class LinkListBlock extends AbstractWidget
{
    public static function scopes(): array
    {
        return ['footer'];
    }

    public function label(): string
    {
        return 'Linklijst';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedListBullet;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop'),
            Repeater::make('items')->label('Links')->schema([
                TextInput::make('label')->label('Label')->required(),
                TextInput::make('href')->label('Link')->placeholder('/over-ons')->required(),
            ])->columns(2)->defaultItems(0)->addActionLabel('Link toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => '', 'items' => []];
    }
}
