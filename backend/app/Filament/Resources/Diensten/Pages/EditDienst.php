<?php

namespace App\Filament\Resources\Diensten\Pages;

use App\Filament\Resources\Diensten\DienstResource;
use App\Support\MediaState;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDienst extends EditRecord
{
    protected static string $resource = DienstResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Dienst verwijderen?')
                ->modalSubmitActionLabel('Definitief verwijderen'),
        ];
    }

    /**
     * FileUpload can't represent the seeded "/images/design/…" paths, so it
     * submits them as empty — without this, saving an untouched form would wipe
     * the design image.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaState::preserveDesignAssets($data, $this->getRecord()->only(['image']));
    }
}
