<?php

namespace App\Filament\Resources\Pages\Concerns;

use App\Models\Page;
use App\Models\PageBlock;
use App\Support\MediaState;
use App\Widgets\WidgetRegistry;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

/**
 * Page-builder glue for the Filament Create & Edit pages.
 *
 * Blocks live in the `page_blocks` table but each widget's fields are namespaced
 * under `{type}` in the form state (so different widgets' fields never collide).
 * We therefore hold the blocks on the form, strip them from the save payload,
 * and upsert them manually. The visual widget picker dispatches the
 * `page-builder::add-block` Livewire event, handled by addBlock().
 */
trait HasWidgetPicker
{
    /** @var array<int|string, array<string, mixed>> */
    protected array $pendingBlocks = [];

    /** Add a fresh block of the chosen widget type to the blocks repeater. */
    #[On('page-builder::add-block')]
    public function addBlock(string $type): void
    {
        $widget = app(WidgetRegistry::class)->find($type);
        $repeater = $this->getBlocksRepeater();

        if ($widget === null || $repeater === null) {
            return;
        }

        $uuid = $repeater->generateUuid() ?? uniqid('block_', true);

        $initial = [
            'type' => $type,
            'is_visible' => true,
            $type => $widget->defaultData(),
        ];

        $items = $repeater->getRawState() ?? [];
        $items[$uuid] = $initial;

        $repeater->rawState($items);
        $repeater->getChildSchema($uuid)->fill($initial);
        $repeater->collapsed(false, shouldMakeComponentCollapsible: false);
        $repeater->callAfterStateUpdated();

        if ($repeater->shouldPartiallyRenderAfterActionsCalled()) {
            $repeater->partiallyRender();
        }
    }

    protected function getBlocksRepeater(): ?Repeater
    {
        foreach ($this->getCachedSchemas() as $schema) {
            foreach ($schema->getFlatComponents(withHidden: true) as $component) {
                if ($component instanceof Repeater && $component->getName() === 'blocks') {
                    return $component;
                }
            }
        }

        return null;
    }

    /**
     * DB blocks -> repeater state (each widget's data namespaced under its type).
     *
     * @param  iterable<PageBlock>  $blocks
     * @return array<string, array<string, mixed>>
     */
    protected function blocksForForm(iterable $blocks): array
    {
        $items = [];

        foreach ($blocks as $block) {
            $items[(string) Str::uuid()] = [
                'id' => $block->id,
                'type' => $block->type,
                'is_visible' => $block->is_visible,
                $block->type => $block->data ?? [],
            ];
        }

        return $items;
    }

    /**
     * Pull blocks off the form payload and stash them for post-save.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function extractBlocks(array $data): array
    {
        $this->pendingBlocks = array_values($data['blocks'] ?? []);
        unset($data['blocks']);

        return $data;
    }

    /** Repeater state -> DB blocks (upsert by id, keep order, prune the rest). */
    protected function persistBlocks(Page $page): void
    {
        $keepIds = [];

        foreach ($this->pendingBlocks as $index => $item) {
            $type = $item['type'] ?? null;

            if ($type === null || ! app(WidgetRegistry::class)->has($type)) {
                continue;
            }

            $record = isset($item['id']) ? PageBlock::find($item['id']) : null;

            $data = (array) ($item[$type] ?? []);

            // FileUpload can't hold the seeded "/images/design/…" paths, so it
            // submits them as empty. Without this, saving any tab of the page
            // would wipe every design image on it.
            if ($record !== null) {
                $data = MediaState::preserveDesignAssets($data, (array) ($record->data ?? []));
            }

            $attributes = [
                'page_id' => $page->id,
                'type' => $type,
                'is_visible' => (bool) ($item['is_visible'] ?? true),
                'sort_order' => $index,
                'data' => $data,
            ];

            if ($record) {
                $record->fill($attributes)->save();
            } else {
                $record = PageBlock::create($attributes);
            }

            $keepIds[] = $record->id;
        }

        $page->blocks()->whereNotIn('id', $keepIds)->delete();
    }
}
