<div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgb(228 228 231); display: flex; justify-content: flex-end;">
    <x-filament::button
        type="button"
        wire:click="saveTab('{{ $tabKey }}')"
        wire:loading.attr="disabled"
        wire:target="saveTab('{{ $tabKey }}')"
    >
        Opslaan
    </x-filament::button>
</div>
