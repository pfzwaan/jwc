<?php

namespace App\Widgets;

use App\Filament\Forms\Components\WebImage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * The stats band (Figma 31847:1537). A #fafafa strip with evenly spread
 * figures: an 80×80 white icon tile, a 45px accent number and a label.
 */
class StatsBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Kerncijfers';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedChartBar;
    }

    public function form(): array
    {
        return [
            ColorPicker::make('accent')->label('Kleur cijfers')->hex()
                ->helperText('Leeg = #ffa425, zoals in het ontwerp.'),

            Repeater::make('stats')
                ->label('Cijfers')
                ->schema([
                    WebImage::make('icon')
                        ->label('Icoon')
                        ->directory('icons')->disk('public')->visibility('public'),
                    TextInput::make('value')->label('Cijfer')->placeholder('2.500+')->required(),
                    TextInput::make('label')->label('Omschrijving')->placeholder('Tevreden klanten'),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Cijfer toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return ['accent' => '', 'stats' => []];
    }
}
