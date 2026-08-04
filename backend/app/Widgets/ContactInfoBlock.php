<?php

namespace App\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Footer contact column. When "use site settings" is on, the frontend fills the
 * address/e-mail/phone from the Instellingen page instead of the fields here —
 * so contact details live in exactly one place.
 */
class ContactInfoBlock extends AbstractWidget
{
    public static function scopes(): array
    {
        return ['footer'];
    }

    public function label(): string
    {
        return 'Contactgegevens';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedPhone;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->placeholder('Contact'),
            Toggle::make('from_settings')
                ->label('Gegevens uit Instellingen halen')
                ->default(true)
                ->helperText('Laat staan om adres, e-mail en telefoon centraal te beheren.'),
            TextInput::make('company')->label('Bedrijfsnaam'),
            TextInput::make('address_line1')->label('Adres — regel 1'),
            TextInput::make('address_line2')->label('Adres — regel 2'),
            TextInput::make('vat')->label('BTW-nummer'),
            TextInput::make('coc')->label('KVK-nummer'),
            TextInput::make('email')->label('E-mailadres'),
            TextInput::make('phone')->label('Telefoonnummer'),
            TextInput::make('fax')->label('Fax'),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => 'Contact', 'from_settings' => true];
    }
}
