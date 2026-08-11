<?php

namespace App\Widgets;

use App\Filament\Forms\Components\WebImage;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * Section 5 — the "Complete ICT-oplossingen" card grid (Figma 31783:6350).
 * Four white cards on a #fafafa band: icon tile, title, short text and a
 * "Meer info" link.
 *
 * Deliberately its own copy rather than a Diensten listing: the design uses
 * shorter summary titles here than the Diensten themselves (section 6 is the
 * CPT-driven one).
 */
class SolutionCardsBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Oplossingen (kaarten)';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedSquares2x2;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->columnSpanFull(),
            Textarea::make('intro')->label('Introtekst')->rows(2)->columnSpanFull(),

            Select::make('columns')
                ->label('Kolommen')
                ->options([2 => '2', 3 => '3', 4 => '4'])
                ->default(4)
                ->selectablePlaceholder(false),

            Repeater::make('cards')
                ->label('Kaarten')
                ->schema([
                    WebImage::make('icon')
                        ->label('Icoon')
                        ->directory('icons')->disk('public')->visibility('public')
                        ->helperText('Bij voorkeur een SVG.'),
                    TextInput::make('title')->label('Titel')->required(),
                    Textarea::make('text')->label('Tekst')->rows(3)->columnSpanFull(),
                    TextInput::make('cta_label')->label('Linktekst')->placeholder('Meer info'),
                    // Plain TextInput — anchors, relative paths and full URLs must all save.
                    TextInput::make('cta_href')->label('Link')->placeholder('/diensten/ict-support'),
                ])
                ->columns(2)
                ->defaultItems(0)
                ->addActionLabel('Kaart toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => '', 'intro' => '', 'columns' => 4, 'cards' => []];
    }
}
