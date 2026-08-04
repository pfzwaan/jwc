<?php

namespace App\Widgets;

use App\Models\Dienst;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * Section 6 — the alternating dienst boxes (Figma 31789:28). A centred heading
 * and intro, then one 1600×781 white card per dienst: accent label pill, title,
 * body, a four-item checklist, an accent button and a 725×646 photo.
 *
 * A CPT listing: pick specific diensten, or leave the selection empty to show
 * the published ones in their admin order.
 */
class DienstBoxesBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Diensten (blokken)';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->columnSpanFull(),
            Textarea::make('intro')->label('Introtekst')->rows(3)->columnSpanFull(),

            Select::make('dienst_ids')
                ->label('Diensten')
                ->multiple()
                ->searchable()
                ->options(fn (): array => Dienst::query()->ordered()->pluck('title', 'id')->all())
                ->helperText('Leeg = alle gepubliceerde diensten, in de volgorde van het overzicht.')
                ->columnSpanFull(),

            TextInput::make('count')
                ->label('Maximum aantal')
                ->numeric()
                ->default(5)
                ->helperText('Alleen van toepassing als hierboven niets is gekozen.'),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => '', 'intro' => '', 'dienst_ids' => [], 'count' => 5];
    }
}
