<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
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

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([
                Section::make('Project')
                    ->description('Naam, samenvatting en de inhoud van de detailpagina.')
                    ->icon(Heroicon::OutlinedWindow)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Naam')
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
                            ->prefix('/projecten/'),
                        // Plain TextInput — relative paths and full URLs must both save.
                        TextInput::make('url')->label('Website')->placeholder('https://klant.nl'),
                        RichEditor::make('excerpt')->label('Samenvatting')->columnSpanFull(),
                        RichEditor::make('body')->label('Omschrijving (detailpagina)')->columnSpanFull(),

                        Repeater::make('categories')
                            ->label('Categorieën')
                            ->schema([TextInput::make('label')->label('Categorie')->required()])
                            ->defaultItems(0)->addActionLabel('Categorie toevoegen')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columnSpanFull(),

                        Repeater::make('skills')
                            ->label('Vaardigheden')
                            ->schema([TextInput::make('label')->label('Vaardigheid')->required()])
                            ->defaultItems(0)->addActionLabel('Vaardigheid toevoegen')
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columnSpanFull(),
                    ]),

                Group::make([
                    Section::make('Publicatie')
                        ->icon(Heroicon::OutlinedEye)
                        ->schema([
                            Toggle::make('is_published')->label('Gepubliceerd')->default(true),
                            Toggle::make('is_featured')->label('Uitgelicht')
                                ->helperText('Geeft de kaart een schaduw.'),
                            TextInput::make('sort_order')->label('Volgorde')->numeric()->default(0)
                                ->helperText('Lager = eerder.'),
                        ]),

                    Section::make('Schermafbeelding')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('image')
                                ->label('Desktop')
                                ->image()
                                ->directory('projects')->disk('public')->visibility('public'),
                            FileUpload::make('image_mobile')
                                ->label('Mobiel (optioneel)')
                                ->image()
                                ->directory('projects')->disk('public')->visibility('public')
                                ->helperText('Voor de telefoon in de header. Leeg = de desktopafbeelding.'),
                            ColorPicker::make('accent_color')->label('Kleur icoon')->hex()
                                ->helperText('Leeg = blauw.'),
                        ]),
                ])->columnSpan(1),
            ]),
        ]);
    }
}
