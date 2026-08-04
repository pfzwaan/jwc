<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\Concerns\HasWidgetPicker;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;

class EditPage extends EditRecord
{
    use HasWidgetPicker;

    protected static string $resource = PageResource::class;

    /** Whether the 'blocks' field was part of the tab just saved (see saveTab()). */
    protected bool $blocksWereSubmitted = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Pagina verwijderen?')
                ->modalDescription('Weet je zeker dat je deze pagina en alle widgets wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')
                ->modalSubmitActionLabel('Definitief verwijderen'),
        ];
    }

    /** No single "save everything" action — each tab has its own (PageForm::tabSaveAction()). */
    protected function getFormActions(): array
    {
        return [];
    }

    /** Save only the fields inside one tab (identified by its Tab::key()). */
    public function saveTab(string $tabKey): void
    {
        // getSchemaComponent() needs the fully-qualified "{schema}.{key}" form.
        $component = $this->getSchemaComponent("form.{$tabKey}", withHidden: true);

        if (! $component instanceof Component) {
            return;
        }

        $this->saveFormComponentOnly($component);

        Notification::make()->title('Opgeslagen')->success()->send();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['blocks'] = $this->blocksForForm($this->getRecord()->blocks);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only the "Widgets" tab's save submits a 'blocks' key — guard so saving
        // the other tabs can't be mistaken for "no blocks" and wipe them.
        $this->blocksWereSubmitted = array_key_exists('blocks', $data);

        return $this->blocksWereSubmitted ? $this->extractBlocks($data) : $data;
    }

    protected function afterSave(): void
    {
        if ($this->blocksWereSubmitted) {
            $this->persistBlocks($this->getRecord());
        }
    }
}
