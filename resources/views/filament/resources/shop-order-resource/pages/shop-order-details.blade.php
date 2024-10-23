<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-white/5 p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                {{ __('Order Details') }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Shop Order ID:') }}</strong> {{ $order->id_shop_order }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Order Reference:') }}</strong> {{ $order->order_reference }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Payment Type:') }}</strong> {{ $order->payment_type }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Carrier:') }}</strong> {{ $order->carrier }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Order State:') }}</strong> {{ $order->order_state }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Total Paid:') }}</strong> {{ $order->total_paid }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <strong>{{ __('Total Shipping:') }}</strong> {{ $order->total_shipping }}
                    </p>
                </div>
            </div>
        </div>



        <!-- Products in Order Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-white/5 p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                {{ __('Products in Order') }}
            </h3>
            <div class="mt-2 max-w-4xl text-sm text-gray-500 dark:text-gray-400">
                <div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200 dark:border-white/5">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b border-b-4 border-gray-200 dark:border-white/5">
                        <tr>
                            <th scope="col" class="px-6 py-3">{{ __('Product Code') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Product Name') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Price') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Quantity') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orderProducts as $product)
                            <tr class="{{ $loop->even ? 'bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }} dark:hover:bg-gray-600 hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->product_price }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $product->quantity }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
