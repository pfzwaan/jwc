<?php

namespace App\Filament\Pages;

use App\Models\BlockLayout;
use App\Widgets\Contracts\Widget as WidgetContract;
use App\Widgets\WidgetRegistry;
use BackedEnum;
use Filament\Forms\Components\Builder as BlockBuilder;
use Filament\Forms\Components\Builder\Block as BuilderBlock;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Editable footer — a Repeater of columns, each holding a Builder of
 * footer-scoped widgets. A single-record page since the site only ever has
 * one footer.
 */
class Footer extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Footer';

    protected static ?string $title = 'Footer';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.footer';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['columns' => $this->columnsToState($this->layout())]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Repeater::make('columns')
                ->label('Kolommen')
                ->reorderableWithDragAndDrop()
                ->collapsible()
                ->collapsed()
                ->addActionLabel('Kolom toevoegen')
                // Label a column by its first widget's heading; widgets without one
                // (e.g. the logo/about column) fall back to the widget's own name.
                ->itemLabel(function (array $state): ?string {
                    $block = $state['blocks'][0] ?? null;

                    if ($block === null) {
                        return null;
                    }

                    $heading = trim((string) ($block['data']['heading'] ?? ''));

                    return $heading !== ''
                        ? $heading
                        : app(WidgetRegistry::class)->find($block['type'] ?? '')?->label();
                })
                ->schema([
                    Toggle::make('is_visible')->label('Zichtbaar')->default(true)->inline(false),
                    BlockBuilder::make('blocks')
                        ->label('Widgets')
                        ->addActionLabel('Widget toevoegen')
                        ->blockNumbers(false)
                        ->blocks($this->widgetBlocks())
                        ->collapsible(),
                ])
                ->defaultItems(0),
        ])->statePath('data');
    }

    /** @return array<int, BuilderBlock> */
    protected function widgetBlocks(): array
    {
        $widgets = app(WidgetRegistry::class)->forScope('footer');

        return collect($widgets)
            ->map(fn (WidgetContract $widget): BuilderBlock => BuilderBlock::make($widget::type())
                ->label($widget->label())
                ->icon($widget->icon())
                ->schema($widget->form()))
            ->values()
            ->all();
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $layout = $this->layout();

        $layout->columns()->delete();

        foreach ($state['columns'] ?? [] as $columnIndex => $columnState) {
            $column = $layout->columns()->create([
                'sort_order' => $columnIndex,
                'is_visible' => $columnState['is_visible'] ?? true,
            ]);

            foreach ($columnState['blocks'] ?? [] as $blockIndex => $blockState) {
                $column->blocks()->create([
                    'type' => $blockState['type'],
                    'data' => $blockState['data'] ?? [],
                    'sort_order' => $blockIndex,
                    'is_visible' => true,
                ]);
            }
        }

        Notification::make()->title('Footer opgeslagen')->success()->send();
    }

    protected function layout(): BlockLayout
    {
        return BlockLayout::firstOrCreate(['key' => 'footer'], ['name' => 'Footer', 'is_active' => true]);
    }

    /** @return array<int, array<string, mixed>> */
    protected function columnsToState(BlockLayout $layout): array
    {
        return $layout->columns->map(fn ($column): array => [
            'is_visible' => $column->is_visible,
            'blocks' => $column->blocks->map(fn ($block): array => [
                'type' => $block->type,
                'data' => $block->data,
            ])->all(),
        ])->all();
    }
}
