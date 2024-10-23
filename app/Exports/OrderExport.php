<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    protected $selectedProducts;

    public function __construct($selectedProducts)
    {
        $this->selectedProducts = $selectedProducts;
    }

    public function collection()
    {
        return collect($this->selectedProducts)->map(function ($item) {
            return [
                'Produkt' => $item['name'],
                'Ilość' => $item['expected_quantity'],
              //  'Cena za sztukę (PLN)' => $item['unit_price'],
              //  'Cena całkowita (PLN)' => $item['quantity'] * $item['unit_price'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Produkt',
            'Ilość',
            'Cena za sztukę (PLN)',
            'Cena całkowita (PLN)',
        ];
    }
}
