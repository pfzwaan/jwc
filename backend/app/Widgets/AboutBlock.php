<?php

namespace App\Widgets;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/** Footer intro column: logo + a short "about" paragraph + social icons. */
class AboutBlock extends AbstractWidget
{
    public static function scopes(): array
    {
        return ['footer'];
    }

    public function label(): string
    {
        return 'Over / logo';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedInformationCircle;
    }

    public function form(): array
    {
        return [
            Toggle::make('show_logo')->label('Logo tonen')->default(true),
            Textarea::make('text')->label('Tekst')->rows(4)->columnSpanFull(),
            Toggle::make('show_socials')
                ->label('Social icons tonen')
                ->default(true)
                ->helperText('De links komen uit Instellingen.'),
        ];
    }

    public function defaultData(): array
    {
        return ['show_logo' => true, 'text' => '', 'show_socials' => true];
    }
}
