<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Order Details Section -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                {{ __('Szczegóły zamówienia') }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-hashtag class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('ID zamówienia w sklepie:') }}</strong> {{ $order->id_shop_order }}
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-receipt-refund class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Numer referencyjny:') }}</strong> {{ $order->order_reference }}
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Typ płatności:') }}</strong> {{ $order->payment_type }}
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-truck class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Przewoźnik:') }}</strong> {{ $order->carrier }}
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Status zamówienia:') }}</strong> {{ $order->order_state }}
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">

                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Łączna kwota:') }}</strong> {{ number_format($order->total_paid, 2, ',', ' ') }} zł
                    </p>
                </div>

                <div class="flex items-center bg-gray-50 dark:bg-gray-900 p-4 rounded-lg transition hover:bg-gray-100 dark:hover:bg-gray-700">
                    <x-heroicon-o-truck class="w-6 h-6 text-gray-600 dark:text-gray-300 mr-3" />
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>{{ __('Koszt wysyłki:') }}</strong> {{ number_format($order->total_shipping, 2, ',', ' ') }} zł
                    </p>
                </div>
            </div>
        </div>

        <!-- Products in Order Section -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-6 w-full">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">

                {{ __('Produkty w zamówieniu') }}
            </h3>

            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="relative overflow-x-auto shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200 border-b-4 border-gray-200 dark:border-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3">

                                {{ __('Kod produktu') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <x-heroicon-o-tag class="inline w-5 h-5 text-gray-600 dark:text-gray-300 mr-2" />
                                {{ __('Nazwa produktu') }}
                            </th>
                            <th scope="col" class="px-6 py-3">

                                {{ __('Cena') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <x-heroicon-o-shopping-bag class="inline w-5 h-5 text-gray-600 dark:text-gray-300 mr-2" />
                                {{ __('Ilość') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orderProducts as $product)
                            <tr class="{{ $loop->even ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }} transition hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium">{{ $product->product_code }}</td>
                                <td class="px-6 py-4">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 text-green-600 dark:text-green-400 font-semibold">
                                    {{ number_format($product->product_price, 2, ',', ' ') }} zł
                                </td>
                                <td class="px-6 py-4">{{ $product->quantity }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
