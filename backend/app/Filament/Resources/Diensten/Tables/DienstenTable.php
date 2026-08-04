<?php

namespace App\Filament\Resources\Diensten\Tables;

use App\Support\MediaUrl;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DienstenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Drag to set the order used by the frontend widgets and the menu.
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')->label('Afbeelding')
                    ->getStateUsing(fn ($record): ?string => MediaUrl::admin($record->image))
                    ->height(56)->width(88)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title')->label('Titel')->searchable()->sortable()->limit(50)->weight('bold'),
                TextColumn::make('label')->label('Label')->badge()->color('gray')->toggleable(),
                IconColumn::make('is_published')->label('Gepubliceerd')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Dienst verwijderen?')
                    ->modalSubmitActionLabel('Definitief verwijderen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
