<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Support\MediaUrl;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')->label('Afbeelding')
                    ->getStateUsing(fn ($record): ?string => MediaUrl::admin($record->image))
                    ->height(56)->width(88)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title')->label('Titel')->searchable()->sortable()->limit(50)->weight('bold'),
                TextColumn::make('category')->label('Categorie')->badge()->color('gray')->toggleable(),
                IconColumn::make('is_published')->label('Gepubliceerd')->boolean(),
                TextColumn::make('published_at')->label('Publicatiedatum')->dateTime('d-m-Y')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Blog verwijderen?')
                    ->modalSubmitActionLabel('Definitief verwijderen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
