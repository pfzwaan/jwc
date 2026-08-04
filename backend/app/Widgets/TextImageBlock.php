<?php

namespace App\Widgets;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

/**
 * Text beside an image (Figma 31847:565 — the "ONZE MISSIE" section on Over ons).
 * Label pill, title, rich body, an optional checklist and an optional button,
 * with the image on either side.
 */
class TextImageBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Tekst + afbeelding';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedViewColumns;
    }

    public function form(): array
    {
        return [
            TextInput::make('label')->label('Label')->placeholder('ONZE MISSIE'),
            TextInput::make('title')->label('Titel')->columnSpanFull(),
            RichEditor::make('body')->label('Tekst')->columnSpanFull(),

            Section::make('Weergave')
                ->columns(3)
                ->schema([
                    Select::make('image_side')
                        ->label('Afbeelding aan')
                        ->options(['right' => 'Rechts', 'left' => 'Links'])
                        ->default('right')
                        ->selectablePlaceholder(false),
                    ColorPicker::make('accent')->label('Accentkleur')->hex()
                        ->helperText('Vinkjes en labelpil. Leeg = merkkleur.'),
                    ColorPicker::make('accent_label_color')->label('Labelkleur')->hex()
                        ->helperText('Leeg = accentkleur.'),
                ]),

            FileUpload::make('image')
                ->label('Afbeelding')
                ->image()
                ->directory('content')->disk('public')->visibility('public')
                ->columnSpanFull(),

            Repeater::make('features')
                ->label('Checklist')
                ->schema([TextInput::make('label')->label('Regel')->required()])
                ->defaultItems(0)
                ->addActionLabel('Regel toevoegen')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->columnSpanFull(),

            TextInput::make('cta_label')->label('Knoptekst (optioneel)'),
            // Plain TextInput — anchors, relative paths and full URLs must all save.
            TextInput::make('cta_href')->label('Knop-link')->placeholder('/contact'),
        ];
    }

    public function defaultData(): array
    {
        return [
            'label' => '',
            'title' => '',
            'body' => '',
            'image' => '',
            'image_side' => 'right',
            'accent' => '',
            'accent_label_color' => '',
            'features' => [],
            'cta_label' => '',
            'cta_href' => '',
        ];
    }
}
