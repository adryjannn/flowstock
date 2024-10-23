<?php

namespace App\Filament\Resources\ShopOrderResource\Pages;

use App\Filament\Resources\ShopOrderResource;
use App\Models\ShopOrder;
use Filament\Resources\Pages\Page;

class ShopOrderDetails extends Page
{
    protected static string $resource = ShopOrderResource::class;
    protected static string $view = 'filament.resources.shop-order-resource.pages.shop-order-details';

    public $order;
    public $orderProducts;
    public $additionalProducts;

    public function mount($record): void
    {
        $this->order = ShopOrder::with('products')->where('id', $record)->firstOrFail();
        $this->orderProducts = $this->order->products;
        $this->additionalProducts = $this->order->additionalProducts;

        //dd($this->order, $this->orderProducts);
    }
}
