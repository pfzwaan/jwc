<?php

namespace App\Filament\Resources\ContactRequests\Pages;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Models\ContactRequest;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactRequest extends ViewRecord
{
    protected static string $resource = ContactRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleHandled')
                ->label(fn (ContactRequest $record): string => $record->is_handled ? 'Heropenen' : 'Markeer als afgehandeld')
                ->icon(fn (ContactRequest $record): Heroicon => $record->is_handled
                    ? Heroicon::OutlinedArrowUturnLeft
                    : Heroicon::OutlinedCheck)
                ->color(fn (ContactRequest $record): string => $record->is_handled ? 'gray' : 'success')
                ->action(fn (ContactRequest $record) => $record->update(['is_handled' => ! $record->is_handled])),
        ];
    }
}
