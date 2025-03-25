<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\Page;

class OrderDetails extends Page
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.order-details';

    public Order $order;

    public function mount($record): void
    {
        $this->order = Order::with('producer', 'items.product', 'orderState') // Załaduj status zamówienia
        ->findOrFail($record);
    }
}
