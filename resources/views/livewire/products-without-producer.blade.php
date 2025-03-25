<div x-data="{ open: false }">
    <x-filament::button class="px-4 py-2" @click="open = !open" style="margin-bottom: 20px">
        {{ __('Products without producer ') . '(' . $productsWithoutProducerCount . ')' }}
    </x-filament::button>

    <div
        x-show="open"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
    >
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-white/5" style="border-radius: 10px">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b border-b-4 border-gray-200 dark:border-white/5">
                <tr>
                    <th scope="col" class="px-6 py-3">{{ __('Name') }}</th>
                    <th scope="col" class="px-6 py-3">{{ __('Product code') }}</th>
                    <th scope="col" class="px-6 py-3">{{ __('Producer') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($products as $product)
                    <tr wire:key="product-{{ $product->id }}" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 dark:hover:bg-gray-900 hover:bg-gray-50 dark:hover:bg-white/5">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->product_name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $product->product_code }}
                        </td>
                        <td class="px-6 py-4">
                            <x-filament::input.wrapper>
                                <x-filament::input.select wire:key="select-{{ $product->id }}" wire:model="selectedProducers.{{ $product->id }}" wire:change="assignProducer({{ $product->id }}, $event.target.value)">
                                    <option value="" selected="selected">-</option>
                                    @foreach($selectProducers as $producer)
                                        <option value="{{ $producer->id }}">{{ $producer->name }}</option>
                                    @endforeach
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-b border-b-4 border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-gray-900 p-6" style="border-radius: 10px">
            <x-filament::pagination :paginator="$products" />
        </div>
    </div>
</div>
