<?php

namespace App\Widgets;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/** Freeform heading + WYSIWYG body. */
class RichTextBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Tekstblok';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedBars3BottomLeft;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop (optioneel)'),
            RichEditor::make('body')->label('Tekst')->columnSpanFull(),
        ];
    }

    public function defaultData(): array
    {
        return ['heading' => '', 'body' => ''];
    }
}
