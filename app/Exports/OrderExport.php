<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function collection()
    {
        return $this->order->items->map(function ($item) {
            return [
                'Produkt' => $item->product->name ?? 'Nieznany produkt',
                'Kod produktu' => $item->product->reference_number ?? 'Brak kodu',
                'Ilość' => (int) $item->quantity,
                'Cena za sztukę (PLN)' => number_format($item->unit_price ?? 0, 2, '.', ''),
                'Cena całkowita (PLN)' => number_format($item->quantity * ($item->unit_price ?? 0), 2, '.', ''),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Produkt',
            'Kod produktu',
            'Ilość',
            'Cena za sztukę (PLN)',
            'Cena całkowita (PLN)',
        ];
    }
}
