<x-filament::page>
    <div class="p-6 w-full bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-700 shadow-md">
        <!-- Nagłówek Zamówienia -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-gray-100">Zamówienie #{{ $order->id }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informacje o Producentcie -->
                <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded-lg shadow-inner">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">Producent</dt>
                    <dd class="mt-1 text-gray-800 dark:text-gray-100">{{ $order->producer->name }}</dd>
                </div>
                <!-- Informacje o Statusie z Badge'em -->
                <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded-lg shadow-inner flex items-center">

                    <dd class="mt-1 ml-2">
                        @if($order->orderState)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium"
                                  style="background-color: {{ $order->orderState->color }}; color: #ffffff;">
                {{ ucfirst($order->orderState->name) }}
            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-300 text-gray-700">
                Brak statusu
            </span>
                        @endif
                    </dd>
                </div>
                <!-- Informacje o Wartości Całkowitej -->
                <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded-lg shadow-inner">
                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Wartość Całkowita</dt>
                    <dd class="mt-1 text-gray-800 dark:text-gray-100">{{ number_format($order->total_value, 2) }} PLN</dd>
                </div>
                <!-- Informacje o Data Utworzenia -->
                <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded-lg shadow-inner">
                    <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Utworzono</dt>
                    <dd class="mt-1 text-gray-800 dark:text-gray-100">{{ $order->created_at->format('Y-m-d H:i') }}</dd>
                </div>
            </div>
        </div>

        <!-- Pozycje Zamówienia -->
        <div>
            <h3 class="text-2xl font-semibold mb-4 mt-3 text-gray-900 dark:text-gray-100">Pozycje Zamówienia</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-200 dark:bg-gray-700 rounded-lg shadow-md">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Produkt
                        </th>
                        <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Ilość
                        </th>
                        <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Cena za sztukę
                        </th>
                        <th class="px-6 py-3 border-b border-gray-300 dark:border-gray-600 bg-gray-300 dark:bg-gray-600 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Cena całkowita
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ number_format($item->unit_price, 2) }} PLN</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-100">{{ number_format($item->quantity * $item->unit_price, 2) }} PLN</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dodatkowe Akcje -->
        <div class="mt-8 flex space-x-4">
            @if($order->pdf_file)
                <a href="{{ Storage::disk('public')->url($order->pdf_file) }}" target="_blank" class="text-black px-4 py-2 mt-3 rounded" style="background-color: rgb(217 119 6); ">
                    {{ __('Pobierz PDF') }}
                </a>
            @endif

            @if($order->xls_file)
                <a href="{{ Storage::disk('public')->url($order->xls_file) }}" target="_blank" class="text-black px-4 py-2 mt-3 rounded" style="background-color: rgb(217 119 6); margin-left: 10px">
                    {{ __('Pobierz Excel') }}
                </a>
            @endif
        </div>
    </div>
</x-filament::page>
