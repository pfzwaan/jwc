<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Support\MediaUrl;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Drag to set the order the widgets list them in.
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')->label('Afbeelding')
                    ->getStateUsing(fn ($record): ?string => MediaUrl::admin($record->image))
                    ->height(56)->width(88)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
                TextColumn::make('title')->label('Naam')->searchable()->sortable()->weight('bold'),
                TextColumn::make('url')->label('Website')->color('gray')->limit(34)->toggleable(),
                IconColumn::make('is_featured')->label('Uitgelicht')->boolean(),
                IconColumn::make('is_published')->label('Gepubliceerd')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Project verwijderen?')
                    ->modalSubmitActionLabel('Definitief verwijderen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
