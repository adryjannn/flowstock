<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
        <div>
            <x-filament::button wire:click="testPrestashopConnection" class="px-4 py-2" @style(['width: 240px'])>
            {{__('Test PrestaShop connection')}}
            </x-filament::button>

            <x-filament::button wire:click="syncPrestashopData" class="px-4 py-2" @style(['width: 200px'])>
            {{__('Synchronize PrestaShop')}}
            </x-filament::button>
        </div>

    </x-filament-panels::form>

    <x-filament-actions::modals />
</x-filament-panels::page>
