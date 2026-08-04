<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->columnSpanFull()->schema([
                // Main column
                Section::make('Artikel')
                    ->description('Titel, samenvatting en de volledige inhoud.')
                    ->icon(Heroicon::OutlinedNewspaper)
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
                            ->prefix('/blog/')
                            ->helperText('Gebruikt in de URL.'),
                        RichEditor::make('excerpt')
                            ->label('Samenvatting')
                            ->helperText('Korte intro, getoond op overzichten en in widgets.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Inhoud')
                            ->columnSpanFull(),
                    ]),

                // Sidebar
                Group::make([
                    Section::make('Publicatie')
                        ->icon(Heroicon::OutlinedEye)
                        ->schema([
                            Toggle::make('is_published')->label('Gepubliceerd')->default(true),
                            DateTimePicker::make('published_at')->label('Publicatiedatum')->default(now()),
                            TextInput::make('author')->label('Auteur')->maxLength(255)->placeholder('bv. Johan Willemsen'),
                            TextInput::make('category')->label('Categorie')->maxLength(255)->placeholder('bv. ICT-tips'),
                        ]),

                    Section::make('Afbeelding')
                        ->icon(Heroicon::OutlinedPhoto)
                        ->schema([
                            FileUpload::make('image')
                                ->label('Afbeelding')->hiddenLabel()
                                ->image()
                                ->directory('blog')->disk('public')->visibility('public')
                                ->helperText('Leeg = placeholder-afbeelding.'),
                        ]),
                ])->columnSpan(1),
            ]),
        ]);
    }
}
