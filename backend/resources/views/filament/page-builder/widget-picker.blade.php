{{-- Visual widget picker. Self-contained styles so it renders consistently
     inside the Filament panel regardless of the compiled CSS. --}}
<div class="fi-fo-component-ctn col-span-full" x-data="{ open: false }">
    <style>
        .wp-add {
            display: flex; width: 100%; align-items: center; justify-content: center; gap: .5rem;
            border: 1.5px dashed rgb(209 213 219); border-radius: .75rem; background: transparent;
            padding: .9rem 1.5rem; font-size: .875rem; font-weight: 600; color: rgb(107 114 128);
            cursor: pointer; transition: border-color .15s, color .15s;
        }
        .wp-add:hover { border-color: rgb(var(--primary-500, 245 158 11)); color: rgb(var(--primary-600, 217 119 6)); }
        .wp-grid { margin-top: .75rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: .75rem; }
        .wp-card {
            display: flex; flex-direction: column; align-items: center; gap: .5rem; text-align: center;
            border: 1px solid rgb(229 231 235); border-radius: .75rem; background: rgb(255 255 255);
            padding: 1rem; font-size: .8125rem; font-weight: 600; color: rgb(55 65 81);
            box-shadow: 0 1px 2px rgb(0 0 0 / .05); cursor: pointer; transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        .wp-card:hover { border-color: rgb(var(--primary-400, 251 191 36)); box-shadow: 0 6px 16px -8px rgb(0 0 0 / .25); transform: translateY(-1px); }
        .wp-card svg { width: 1.75rem; height: 1.75rem; color: rgb(var(--primary-500, 245 158 11)); }
        .dark .wp-add { border-color: rgb(75 85 99); color: rgb(156 163 175); }
        .dark .wp-card { background: rgb(31 41 55); border-color: rgb(55 65 81); color: rgb(209 213 219); }
    </style>

    <button type="button" x-on:click="open = !open" class="wp-add">
        <x-heroicon-o-plus-circle style="width:1.25rem;height:1.25rem" />
        Widget toevoegen
    </button>

    <div x-show="open" x-cloak class="wp-grid">
        @foreach ($widgets as $widget)
            <button
                type="button"
                wire:click="$dispatch('page-builder::add-block', { type: '{{ $widget['type'] }}' })"
                x-on:click="open = false"
                class="wp-card"
            >
                <x-dynamic-component :component="$widget['icon']" />
                {{ $widget['label'] }}
            </button>
        @endforeach
    </div>
</div>
