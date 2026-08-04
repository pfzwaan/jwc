<?php

namespace App\Widgets;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

/**
 * Section 7 — customer reviews (Figma 31789:145). A #fafafa band with a centred
 * heading, intro and Trustpilot rating summary, then a row of 388×315 review
 * cards.
 */
class ReviewsBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Reviews';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedStar;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->columnSpanFull(),
            Textarea::make('intro')->label('Introtekst')->rows(2)->columnSpanFull(),

            Section::make('Beoordeling')
                ->description('De samenvattingsregel boven de reviews.')
                ->columns(3)
                ->schema([
                    Toggle::make('show_rating')->label('Tonen')->default(true)->columnSpanFull(),
                    TextInput::make('rating_label')->label('Oordeel')->placeholder('Great'),
                    TextInput::make('rating_score')->label('Sterren')->numeric()->minValue(0)->maxValue(5)->default(5),
                    TextInput::make('rating_count')->label('Aantal reviews')->numeric(),
                    // Plain TextInput — relative paths and full URLs must both save.
                    TextInput::make('rating_url')->label('Link naar Trustpilot')->columnSpanFull(),
                ]),

            Repeater::make('reviews')
                ->label('Reviews')
                ->schema([
                    TextInput::make('name')->label('Naam')->required(),
                    DatePicker::make('date')->label('Datum')->native(false)->displayFormat('d M Y'),
                    Select::make('rating')
                        ->label('Sterren')
                        ->options([5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1'])
                        ->default(5)
                        ->selectablePlaceholder(false),
                    Textarea::make('text')->label('Review')->rows(3)->columnSpanFull(),
                ])
                ->columns(3)
                ->defaultItems(0)
                ->addActionLabel('Review toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return [
            'heading' => '',
            'intro' => '',
            'show_rating' => true,
            'rating_label' => 'Great',
            'rating_score' => 5,
            'rating_count' => null,
            'rating_url' => '',
            'reviews' => [],
        ];
    }
}
