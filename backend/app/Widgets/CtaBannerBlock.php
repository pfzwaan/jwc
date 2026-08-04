<?php

namespace App\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Section 9 — the dark CTA banner (Figma 31880:35). A 1601×493 #111 card with
 * rounded-[30px] corners, a decorative arc pattern and centred title, body and
 * button.
 */
class CtaBannerBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'CTA-banner';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedMegaphone;
    }

    public function form(): array
    {
        return [
            TextInput::make('title')->label('Titel')->columnSpanFull(),
            Textarea::make('body')->label('Tekst')->rows(2)->columnSpanFull(),
            TextInput::make('cta_label')->label('Knoptekst')->placeholder('Start remote support'),
            // Plain TextInput — anchors, relative paths and full URLs must all save.
            TextInput::make('cta_href')->label('Knop-link')->placeholder('/contact'),
            Toggle::make('show_pattern')->label('Achtergrondpatroon tonen')->default(true),
        ];
    }

    public function defaultData(): array
    {
        return ['title' => '', 'body' => '', 'cta_label' => '', 'cta_href' => '', 'show_pattern' => true];
    }
}
