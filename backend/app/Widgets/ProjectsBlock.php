<?php

namespace App\Widgets;

use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

/**
 * Realised projects — "browser window" cards with the traffic-light dots,
 * a screenshot and the site name.
 *
 * A CPT listing: pick specific projects or leave the selection empty to show
 * the published ones in overview order. Two layouts from the same widget:
 * `slider` (home, Figma 31793:2030) and `grid` (Website & Hosting, 31848:2205).
 */
class ProjectsBlock extends AbstractWidget
{
    public function label(): string
    {
        return 'Projecten';
    }

    public function icon(): string|Heroicon
    {
        return Heroicon::OutlinedWindow;
    }

    public function form(): array
    {
        return [
            TextInput::make('heading')->label('Kop')->columnSpanFull(),
            Textarea::make('intro')->label('Introtekst')->rows(2)->columnSpanFull(),

            Select::make('layout')
                ->label('Weergave')
                ->options(['slider' => 'Slider', 'grid' => 'Raster'])
                ->default('slider')
                ->selectablePlaceholder(false),

            Select::make('columns')
                ->label('Kolommen (raster)')
                ->options([2 => '2', 3 => '3', 4 => '4'])
                ->default(3)
                ->selectablePlaceholder(false),

            Select::make('project_ids')
                ->label('Projecten')
                ->multiple()
                ->searchable()
                ->options(fn (): array => Project::query()->ordered()->pluck('title', 'id')->all())
                ->helperText('Leeg = alle gepubliceerde projecten, in de volgorde van het overzicht.')
                ->columnSpanFull(),

            TextInput::make('count')
                ->label('Maximum aantal')
                ->numeric()
                ->default(9)
                ->helperText('Alleen van toepassing als hierboven niets is gekozen.'),

            TextInput::make('cta_label')->label('Knoptekst')->placeholder('Bekijk alle projecten'),
            // Plain TextInput — anchors, relative paths and full URLs must all save.
            TextInput::make('cta_href')->label('Knop-link')->placeholder('/projecten'),
        ];
    }

    public function defaultData(): array
    {
        return [
            'heading' => '',
            'intro' => '',
            'layout' => 'slider',
            'columns' => 3,
            'project_ids' => [],
            'count' => 9,
            'cta_label' => '',
            'cta_href' => '',
        ];
    }
}
