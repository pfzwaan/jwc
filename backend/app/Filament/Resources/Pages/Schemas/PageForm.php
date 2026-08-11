<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Forms\Components\WebImage;
use App\Models\Page;
use App\Widgets\WidgetRegistry;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

/**
 * Three tabs: Algemeen (incl. menu), Widgets (the draggable page builder) and
 * SEO. Each tab saves on its own, so editing one never touches the others.
 */
class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->tabs([
                self::generalTab(),
                self::widgetsTab(),
                self::seoTab(),
            ]),
        ]);
    }

    /**
     * Save button for a single tab — saves only that tab's fields
     * (EditPage::saveTab()). A plain wire:click button, not a Filament Action:
     * actions mount before calling their handler, which added a redundant
     * network round trip for no benefit here.
     */
    private static function tabSaveAction(string $tabKey): View
    {
        return View::make('filament.pages.components.tab-save-button')
            ->columnSpanFull()
            ->viewData(['tabKey' => $tabKey]);
    }

    private static function generalTab(): Tab
    {
        return Tab::make('Algemeen')->key('tab-general')->schema([
            TextInput::make('name')
                ->label('Naam (menu-label)')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $operation, $state, callable $set): void {
                    if ($operation === 'create') {
                        $set('slug', Str::slug((string) $state));
                    }
                }),

            TextInput::make('title')
                ->label('Paginatitel (H1)')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('URL, bv. /diensten. Gebruik "home" voor de homepage.'),

            Select::make('parent_id')
                ->label('Valt onder (menu)')
                ->placeholder('— Hoofdmenu —')
                ->searchable()
                ->options(fn (?Page $record): array => Page::query()
                    ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                    ->orderBy('menu_sort')
                    ->pluck('name', 'id')
                    ->all())
                ->helperText('Kies een bovenliggende pagina om deze als submenu-item te tonen.'),

            Toggle::make('is_published')->label('Gepubliceerd')->default(true),

            Section::make('Menu')
                ->description('Hoe deze pagina in het hoofdmenu verschijnt.')
                ->columnSpanFull()
                ->schema([
                    Toggle::make('menu_visibility')->label('Tonen in hoofdmenu')->default(true),

                    TextInput::make('menu_sort')->label('Menu-volgorde')->numeric()->default(0)
                        ->helperText('Lager = eerder. Ook te ordenen door rijen te slepen in het overzicht.'),

                    // Optional href override — anchors (#), relative paths and full URLs must all save,
                    // so this stays a plain TextInput (no ->url() rule). Leeg = de eigen paginalink.
                    TextInput::make('menu_link')->label('Menu-link (optioneel)')
                        ->placeholder('/diensten/webdesign')
                        ->helperText('Laat dit menu-item naar een andere pagina wijzen. Leeg = eigen pagina.'),

                    Select::make('menu_dropdown_type')
                        ->label('Menu-type')
                        ->live()
                        ->default('none')
                        ->selectablePlaceholder(false)
                        ->options([
                            'none' => 'Gewone link',
                            'dropdown' => "Dropdown (subpagina's)",
                            'mega' => 'Mega-menu (kolommen + kaart)',
                        ])
                        ->helperText("Bepaalt hoe subpagina's onder dit item verschijnen."),

                    // Auto-list diensten as submenu items instead of manual subpages.
                    Toggle::make('menu_diensten')
                        ->label('Diensten automatisch tonen')
                        ->visible(fn (Get $get): bool => in_array($get('menu_dropdown_type'), ['dropdown', 'mega'], true))
                        ->helperText("Toon alle gepubliceerde diensten als submenu — geen losse subpagina's nodig."),

                    // Bottom CTA button — shown for dropdown & mega menus.
                    Section::make('Menu-knop (optioneel)')
                        ->description('Knop onderaan de dropdown / het mega-menu.')
                        ->visible(fn (Get $get): bool => in_array($get('menu_dropdown_type'), ['dropdown', 'mega'], true))
                        ->columns(2)
                        ->schema([
                            TextInput::make('menu_dropdown_cta.label')->label('Knoptekst'),
                            // Plain TextInput — anchors (#), relative paths (/contact) and full URLs must all save.
                            TextInput::make('menu_dropdown_cta.href')->label('Link')->placeholder('/diensten'),
                        ]),

                    // Featured card — mega menu only.
                    Section::make('Uitgelichte kaart (mega-menu)')
                        ->description('De kaart rechts in het mega-menu.')
                        ->visible(fn (Get $get): bool => $get('menu_dropdown_type') === 'mega')
                        ->columns(2)
                        ->schema([
                            TextInput::make('menu_featured_card.badge')->label('Label')->placeholder('Populair'),
                            TextInput::make('menu_featured_card.title')->label('Titel'),
                            Textarea::make('menu_featured_card.text')->label('Tekst')->rows(2)->columnSpanFull(),
                            WebImage::make('menu_featured_card.image')->label('Afbeelding')
                                ->directory('menu')->disk('public')->visibility('public')
                                ->helperText('Leeg = subtiele placeholder.'),
                            TextInput::make('menu_featured_card.cta_label')->label('Knoptekst'),
                            TextInput::make('menu_featured_card.cta_href')->label('Knop-link')->placeholder('/diensten'),
                        ]),
                ]),
            self::tabSaveAction('tab-general'),
        ]);
    }

    private static function widgetsTab(): Tab
    {
        return Tab::make('Widgets')->key('tab-widgets')->schema([
            // Visual widget picker — dispatches page-builder::add-block.
            self::blockPicker(),

            // Draggable list of widgets. Each widget's fields are namespaced under
            // its own `type`, shown only for the matching block.
            Repeater::make('blocks')
                ->hiddenLabel()
                ->addable(false)
                ->defaultItems(0)
                ->collapsible()
                ->collapsed()
                ->cloneable()
                ->reorderableWithDragAndDrop()
                ->reorderableWithButtons()
                ->columnSpanFull()
                ->deleteAction(fn (Action $action) => $action->requiresConfirmation()
                    ->modalHeading('Widget verwijderen?')
                    ->modalDescription('Weet je zeker dat je deze widget wilt verwijderen?')
                    ->modalSubmitActionLabel('Verwijderen'))
                ->itemLabel(fn (array $state): ?string => app(WidgetRegistry::class)->find($state['type'] ?? '')?->label() ?? 'Onbekende widget')
                ->schema([
                    Hidden::make('type'),
                    Hidden::make('is_visible')->default(true),
                    ...collect(app(WidgetRegistry::class)->forScope('page'))
                        ->map(fn ($widget, string $type) => Group::make($widget->form())
                            ->statePath($type)
                            ->key("widget-{$type}")
                            ->visible(fn (Get $get): bool => ($get('type') ?? '') === $type))
                        ->values()->all(),
                ]),
            self::tabSaveAction('tab-widgets'),
        ]);
    }

    private static function blockPicker(): View
    {
        return View::make('filament.page-builder.widget-picker')
            ->columnSpanFull()
            ->viewData(['widgets' => app(WidgetRegistry::class)->toCards('page')]);
    }

    private static function seoTab(): Tab
    {
        return Tab::make('SEO')->key('tab-seo')->schema([
            TextInput::make('seo.title')->label('SEO-titel')->maxLength(255)
                ->helperText('Leeg = paginatitel.'),
            Textarea::make('seo.description')->label('SEO-omschrijving')->rows(3)->maxLength(300),
            self::tabSaveAction('tab-seo'),
        ]);
    }
}
