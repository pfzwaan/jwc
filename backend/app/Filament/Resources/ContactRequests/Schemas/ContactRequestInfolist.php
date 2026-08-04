<?php

namespace App\Filament\Resources\ContactRequests\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Aanvrager')
                ->icon(Heroicon::OutlinedUser)
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Naam'),
                    TextEntry::make('email')->label('E-mailadres')->copyable(),
                    TextEntry::make('phone')->label('Telefoonnummer')->placeholder('—')->copyable(),
                    TextEntry::make('company')->label('Bedrijf')->placeholder('—'),
                ]),

            Section::make('Bericht')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->schema([
                    TextEntry::make('subject')->label('Onderwerp')->placeholder('—'),
                    TextEntry::make('message')->label('Bericht')->placeholder('—')->columnSpanFull(),
                ]),

            Section::make('Herkomst')
                ->icon(Heroicon::OutlinedInformationCircle)
                ->columns(3)
                ->schema([
                    TextEntry::make('source')->label('Pagina')->placeholder('—'),
                    TextEntry::make('created_at')->label('Ontvangen')->dateTime('d-m-Y H:i'),
                    IconEntry::make('is_handled')->label('Afgehandeld')->boolean(),
                ]),
        ]);
    }
}
