<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgb(228 228 231); display: flex; justify-content: flex-end;">
            <x-filament::button type="submit">
                Opslaan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
