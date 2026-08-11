<?php

namespace App\Filament\Resources\Diensten\Schemas;

use App\Filament\Forms\Components\WebImage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class DienstForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([
                // Main column
                Section::make('Dienst')
                    ->description('Titel, samenvatting en de volledige inhoud.')
                    ->icon(Heroicon::OutlinedWrenchScrewdriver)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, callable $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefix('/diensten/')
                            ->helperText('Gebruikt in de URL.'),
                        TextInput::make('label')
                            ->label('Label')
                            ->maxLength(255)
                            ->placeholder('ICT SUPPORT')
                            ->helperText('Het pill-label boven de titel.'),
                        RichEditor::make('excerpt')
                            ->label('Samenvatting')
                            ->helperText('Korte intro, getoond op overzichten en in widgets.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Inhoud')
                            ->columnSpanFull(),

                        Repeater::make('features')
                            ->label('Checklist')
                            ->schema([
                                TextInput::make('label')->label('Regel')->required(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Regel toevoegen')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columnSpanFull(),
                    ]),

                // Sidebar
                Group::make([
                    Section::make('Publicatie')
                        ->icon(Heroicon::OutlinedEye)
                        ->schema([
                            Toggle::make('is_published')->label('Gepubliceerd')->default(true),
                            TextInput::make('sort_order')->label('Volgorde')->numeric()->default(0)
                                ->helperText('Lager = eerder.'),
                        ]),

                    Section::make('Afbeelding')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            WebImage::make('image')
                                ->label('Afbeelding')->hiddenLabel()
                                ->directory('diensten')->disk('public')->visibility('public')
                                ->helperText('Leeg = placeholder-afbeelding.'),
                            TextInput::make('icon')->label('Icoon')->maxLength(255)
                                ->helperText('Naam van het icoon in de frontend, bv. "support".'),
                        ]),

                    Section::make('Accentkleur')
                        ->description('Bepaalt de knop, de vinkjes en de labelpil van dit blok.')
                        ->icon(Heroicon::OutlinedSwatch)
                        ->schema([
                            ColorPicker::make('accent_color')->label('Accentkleur')->hex(),
                            ColorPicker::make('accent_label_color')->label('Labelkleur')->hex()
                                ->helperText('Leeg = dezelfde als de accentkleur.'),
                        ]),

                    Section::make('Knop')
                        ->icon(Heroicon::OutlinedCursorArrowRays)
                        ->schema([
                            TextInput::make('cta_label')->label('Knoptekst')->placeholder('Meer informatie'),
                            // Plain TextInput — anchors, relative paths and full URLs must all save.
                            TextInput::make('cta_href')->label('Knop-link')->placeholder('/diensten/ict-support'),
                        ]),
                ])->columnSpan(1),
            ]),
        ]);
    }
}
