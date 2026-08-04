<?php

namespace App\Widgets;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Contact / offerte form. Drop it on any page; submissions land in the
 * "Aanvragen" resource. The optional details column reads the address, e-mail
 * and phone from Instellingen, so contact data lives in exactly one place.
 */
class ContactFormBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Contactformulier';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedEnvelope;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->columnSpanFull(),
            Textarea::make('intro')->label('Introtekst')->rows(2)->columnSpanFull(),

            Toggle::make('show_details')
                ->label('Contactgegevens tonen')
                ->default(true)
                ->helperText('Toont adres, e-mail en telefoon uit Instellingen naast het formulier.'),
            TextInput::make('details_heading')->label('Kop bij de gegevens')->placeholder('Onze gegevens'),

            TextInput::make('submit_label')->label('Knoptekst')->placeholder('Verstuur bericht'),

            Repeater::make('subjects')
                ->label('Onderwerpen')
                ->schema([
                    TextInput::make('label')->label('Onderwerp')->required(),
                ])
                ->defaultItems(0)
                ->addActionLabel('Onderwerp toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->helperText('Leeg = geen keuzelijst in het formulier.')
                ->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return [
            'heading' => '',
            'intro' => '',
            'show_details' => true,
            'details_heading' => 'Onze gegevens',
            'submit_label' => 'Verstuur bericht',
            'subjects' => [],
        ];
    }
}
