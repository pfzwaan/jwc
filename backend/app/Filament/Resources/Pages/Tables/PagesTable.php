<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Drag to reorder the menu; tree order keeps children under parents.
            ->reorderable('menu_sort')
            ->modifyQueryUsing(fn ($query) => $query->orderByRaw('COALESCE(parent_id, id)')->orderBy('parent_id')->orderBy('menu_sort'))
            ->columns([
                TextColumn::make('name')
                    ->label('Pagina')
                    ->searchable()
                    ->formatStateUsing(function ($state, Page $record): string {
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $record->depth());
                        $chevron = $record->depth() > 0 ? '↳ ' : '';

                        return $indent.$chevron.e($state);
                    })
                    ->html(),

                TextColumn::make('slug')->label('URL')
                    ->formatStateUsing(fn (Page $record): string => $record->fullPath())
                    ->color('gray')->toggleable(),

                TextColumn::make('menu_dropdown_type')
                    ->label('Menu-type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dropdown' => 'Dropdown',
                        'mega' => 'Mega-menu',
                        default => 'Link',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'dropdown' => 'success',
                        'mega' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('menu_visibility')->label('In menu')->boolean(),
                IconColumn::make('is_published')->label('Live')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),

                // Duplicate the page including all of its widgets. The copy is
                // hidden from the menu until the editor is ready to publish it.
                Action::make('duplicate')
                    ->label('Dupliceren')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Pagina dupliceren?')
                    ->modalDescription(fn (Page $record): string => "Er wordt een kopie van “{$record->name}” gemaakt, inclusief alle widgets. De kopie staat niet meteen in het menu.")
                    ->modalSubmitActionLabel('Dupliceren')
                    ->action(function (Page $record): void {
                        $copy = $record->replicate(['slug']);
                        $copy->name = $record->name.' (kopie)';
                        $copy->slug = self::uniqueSlug($record->slug);
                        $copy->menu_visibility = false;
                        $copy->is_published = false;
                        $copy->save();

                        foreach ($record->blocks as $block) {
                            $copy->blocks()->create([
                                'type' => $block->type,
                                'data' => $block->data,
                                'sort_order' => $block->sort_order,
                                'is_visible' => $block->is_visible,
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Pagina gedupliceerd')
                            ->body("“{$copy->name}” is aangemaakt als concept.")
                            ->send();
                    }),

                DeleteAction::make()
                    ->modalHeading('Pagina verwijderen?')
                    ->modalDescription(fn (Page $record): string => "Weet je zeker dat je “{$record->name}” en alle bijbehorende widgets wilt verwijderen? Dit kan niet ongedaan worden gemaakt.")
                    ->modalSubmitActionLabel('Definitief verwijderen'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** A slug not yet used by another page: "diensten-kopie", "-kopie-2", … */
    private static function uniqueSlug(string $base): string
    {
        $slug = $base.'-kopie';
        $i = 2;

        while (Page::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-kopie-'.$i;
            $i++;
        }

        return $slug;
    }
}
