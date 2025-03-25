<?php

namespace App\Livewire;

use App\Models\Producer;
use App\Models\Product;
use App\Models\ShopOrderProduct;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsWithoutProducer extends Component
{
    use WithPagination;

    public $selectedProducers = [];

    public function render()
    {
        return view('livewire.products-without-producer', [
            'products' => $this->getProductsWithoutProducer(),
            'selectProducers' => Producer::orderBy('name', 'asc')->get(),
            'productsWithoutProducerCount' => $this->getProductsWithoutProducer()->count(),
        ]);
    }

    public function getProductsWithoutProducer()
    {
        return ShopOrderProduct::select('product_code', DB::raw('MIN(id) as id'), 'product_name', 'product_price')
            ->whereNotIn('product_code', Product::pluck('reference_number'))
            ->groupBy('product_code', 'product_name', 'product_price')
            ->paginate(10);
    }

    public function assignProducer($orderProductId, $producerId)
    {
        $orderProduct = ShopOrderProduct::find($orderProductId);

        if ($orderProduct && $producerId) {
            $product = Product::where('reference_number', $orderProduct->product_code)->first();

            if (!$product) {
                $product = Product::create([
                    'reference_number' => $orderProduct->product_code,
                    'name' => $orderProduct->product_name,
                    'description' => '',
                    'stock_available' => 0,
                    'producer_id' => $producerId,
                    'wholesale_price' => $orderProduct->product_price,
                ]);
            } else {
                $product->producer_id = $producerId;
                $product->save();
            }

            unset($this->selectedProducers[$orderProductId]);

            Notification::make()
                ->success()
                ->title(__('Producent przypisany poprawnie'))
                ->send();
        }
    }
}
