<?php

namespace App\Widgets;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Section 4 — the client logo strip (Figma 31774:6255). A single evenly spread
 * row, 147px tall, logos vertically centred.
 */
class LogosBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Logo-balk';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedBuildingOffice2;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop (optioneel)')->columnSpanFull(),

            Toggle::make('grayscale')
                ->label('Logo\'s in grijstinten tonen')
                ->default(false)
                ->helperText('Logo\'s kleuren in bij hover.'),

            Repeater::make('logos')
                ->label('Logo\'s')
                ->schema([
                    FileUpload::make('image')
                        ->label('Logo')
                        ->image()
                        ->directory('logos')->disk('public')->visibility('public'),
                    TextInput::make('name')->label('Naam')->helperText('Gebruikt als alt-tekst.'),
                    // Plain TextInput — relative paths and full URLs must both save.
                    TextInput::make('href')->label('Link (optioneel)')->placeholder('https://klant.nl'),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Logo toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => '', 'grayscale' => false, 'logos' => []];
    }
}
