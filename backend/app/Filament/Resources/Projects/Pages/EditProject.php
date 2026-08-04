<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Support\MediaState;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Project verwijderen?')
                ->modalSubmitActionLabel('Definitief verwijderen'),
        ];
    }

    /**
     * FileUpload can't represent the seeded "/images/design/…" paths, so it
     * submits them as empty — without this, saving an untouched form would wipe
     * the design screenshot.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaState::preserveDesignAssets($data, $this->getRecord()->only(['image']));
    }
}
