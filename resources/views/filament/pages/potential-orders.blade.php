<x-filament::page>
    <h2 class="text-xl font-bold">Potential Orders - Lista Producentów</h2>

    {{ $this->table }}

    <h2 class="text-xl font-bold mt-6">Dodaj brakujące produkty do magazynu</h2>

    @livewire('products-without-producer')
</x-filament::page>
