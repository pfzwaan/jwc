<?php

namespace App\Widgets;

use App\Widgets\Contracts\Widget;
use InvalidArgumentException;

/**
 * Central catalogue of page widgets. Register the widget classes once (in
 * AppServiceProvider); the page builder pulls the list for its picker and looks
 * up individual widgets by `type` when adding/loading blocks.
 */
class WidgetRegistry
{
    /** @var array<string, Widget> */
    private array $widgets = [];

    public function register(string $widgetClass): void
    {
        if (! is_subclass_of($widgetClass, Widget::class)) {
            throw new InvalidArgumentException("{$widgetClass} must implement ".Widget::class);
        }

        /** @var Widget $instance */
        $instance = app($widgetClass);
        $this->widgets[$instance::type()] = $instance;
    }

    /** @param array<int, class-string<Widget>> $widgetClasses */
    public function registerMany(array $widgetClasses): void
    {
        foreach ($widgetClasses as $class) {
            $this->register($class);
        }
    }

    public function find(string $type): ?Widget
    {
        return $this->widgets[$type] ?? null;
    }

    public function has(string $type): bool
    {
        return isset($this->widgets[$type]);
    }

    /** @return array<string, Widget> */
    public function all(): array
    {
        return $this->widgets;
    }

    /** @return array<string, Widget> */
    public function forScope(string $scope): array
    {
        return array_filter(
            $this->widgets,
            fn (Widget $widget): bool => in_array($scope, $widget::scopes(), true),
        );
    }

    /** @return array<int, array{type: string, label: string, icon: mixed}> */
    public function toCards(?string $scope = null): array
    {
        $widgets = $scope ? $this->forScope($scope) : $this->widgets;

        return array_values(array_map(
            fn (Widget $widget): array => [
                'type' => $widget::type(),
                'label' => $widget->label(),
                // Blade <x-dynamic-component> needs the full "heroicon-o-…" name;
                // the enum's value is just "o-…".
                'icon' => $widget->icon() instanceof \Filament\Support\Icons\Heroicon
                    ? 'heroicon-'.$widget->icon()->value
                    : $widget->icon(),
            ],
            $widgets,
        ));
    }
}
