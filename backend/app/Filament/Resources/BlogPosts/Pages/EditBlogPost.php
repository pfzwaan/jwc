<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Support\MediaState;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Blog verwijderen?')
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
