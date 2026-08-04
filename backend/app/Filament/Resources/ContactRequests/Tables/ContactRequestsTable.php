<?php

namespace App\Filament\Resources\ContactRequests\Tables;

use App\Models\ContactRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Ontvangen')->dateTime('d-m-Y H:i')->sortable(),
                TextColumn::make('name')->label('Naam')->searchable()->weight('bold'),
                TextColumn::make('email')->label('E-mailadres')->searchable()->copyable(),
                TextColumn::make('subject')->label('Onderwerp')->limit(40)->placeholder('—'),
                IconColumn::make('is_handled')->label('Afgehandeld')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_handled')
                    ->label('Afgehandeld')
                    ->placeholder('Alle aanvragen')
                    ->trueLabel('Alleen afgehandeld')
                    ->falseLabel('Alleen open'),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('toggleHandled')
                    ->label(fn (ContactRequest $record): string => $record->is_handled ? 'Heropenen' : 'Afgehandeld')
                    ->icon(fn (ContactRequest $record): Heroicon => $record->is_handled
                        ? Heroicon::OutlinedArrowUturnLeft
                        : Heroicon::OutlinedCheck)
                    ->color(fn (ContactRequest $record): string => $record->is_handled ? 'gray' : 'success')
                    ->action(fn (ContactRequest $record) => $record->update(['is_handled' => ! $record->is_handled])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
