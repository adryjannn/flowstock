<x-filament-panels::page>
    <div class="p-6 w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700" style="margin: auto;">
        <h2 class="text-xl font-bold mb-4">{{ __('Produkty producenta: ') . $producer->name }}</h2>

        <div class="mb-6 flex justify-between items-center">
            <button wire:click="toggleSelectAll" class="bg-blue-500 text-white px-4 py-2 rounded">
                {{ $selectAll ? __('Odznacz wszystkie') : __('Zaznacz wszystkie') }}
            </button>

            <x-filament::input.wrapper inline-prefix prefix-icon="heroicon-m-magnifying-glass" class="w-1/3">
                <x-filament::input
                    type="text"
                    placeholder="{{ __('Szukaj produktu') }}"
                    wire:model.defer="searchTerm"
                    wire:keydown.debounce.500ms="filterData"
                />
            </x-filament::input.wrapper>
        </div>

        <div class="relative overflow-x-auto shadow-md rounded-b-xl border border-gray-200 dark:border-white/5" style="max-height: 60vh; overflow-y: auto;">
            <table class="w-full text-sm text-left text-gray-950 dark:text-white">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-white border-b border-b-4 border-gray-200 dark:border-white/5">
                <tr>
                    <th class="p-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:click="toggleSelectAll" class="fi-checkbox-input rounded border-none bg-white shadow-sm ring-1 transition duration-75 checked:ring-0 focus:ring-2">
                            <label class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th class="px-6 py-3">{{ __('Kod produktu') }}</th>
                    <th class="px-6 py-3">{{ __('Nazwa') }}</th>
                    <th class="px-6 py-3">{{ __('Ilość do zamówienia') }}</th>
                    <th class="px-6 py-3">{{ __('Sprzedano') }}</th>
                    <th class="px-6 py-3">{{ __('Stan magazynowy') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($productsForOrder as $index => $product)
                    <tr class="@if($product['selected']) bg-blue-100 dark:bg-blue-900 @endif border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="p-4">
                            <input type="checkbox" wire:click="toggleProductSelection({{ $index }})" {{ $product['selected'] ? 'checked' : '' }} class="fi-checkbox-input rounded">
                        </td>
                        <td class="px-6 py-4">{{ $product['reference_number'] }}</td>
                        <td class="px-6 py-4">{{ $product['name'] }}</td>
                        <td class="px-6 py-4">
                            <input type="number" wire:change="updateProductQuantity({{ $index }}, $event.target.value)" value="{{ $product['expected_quantity'] }}" min="0" class="w-20 border rounded">
                        </td>
                        <td class="px-6 py-4">{{ $product['sold_quantity'] }}</td>
                        <td class="px-6 py-4">{{ $product['stock_available'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex justify-start space-x-4">
        <x-filament::button wire:click="generateOrder" class="bg-green-500 text-white px-4 py-2 rounded">
            {{ __('Generuj zamówienie') }}
        </x-filament::button>
    </div>
</x-filament-panels::page>
