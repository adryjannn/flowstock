<?php

namespace App\Filament\Pages;

use App\Exports\OrderExport;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Producer;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CreateOrderToProducer extends Page
{
    protected static string $view = 'filament.pages.create-order-to-producer';
    protected static bool $shouldRegisterNavigation = false;

    public array $productsForOrder = [];
    public ?Producer $producer = null;
    public bool $selectAll = false;
    public string $searchTerm = '';

    public function mount(Request $request)
    {
        $producerId = $request->query('producerId');
        $this->producer = Producer::find($producerId);

        if (!$this->producer) {
            return Redirect::route('filament.pages.potential-orders');
        }

        $this->productsForOrder = $this->getProductsForOrder($this->producer->id);
    }

    public function getProductsForOrder(int $producerId): array
    {
        try {
            if (!is_numeric($producerId) || $producerId <= 0) {
                Log::warning("Invalid producer ID provided: {$producerId}");
                return [];
            }

            return Product::query()
                ->where('producer_id', $producerId)
                ->leftJoin('shop_orders_product as sop', 'products.reference_number', '=', 'sop.product_code')
                ->select([
                    'products.id',
                    'products.name',
                    'products.reference_number',
                    'products.stock_available',
                    DB::raw('COALESCE(SUM(sop.quantity), 0) as sold_quantity')
                ])
                ->groupBy('products.id', 'products.name', 'products.reference_number', 'products.stock_available')
                ->get()
                ->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'reference_number' => $product->reference_number,
                    'stock_available' => (int) $product->stock_available,
                    'sold_quantity' => (int) $product->sold_quantity,
                    'expected_quantity' => 0,
                    'selected' => false,
                ])
                ->toArray();
        } catch (\Exception $e) {
            Log::error("Error fetching products for producer ID {$producerId}: " . $e->getMessage());
            return [];
        }
    }



    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;

        foreach ($this->productsForOrder as &$product) {
            $product['selected'] = $this->selectAll;
        }
    }


    public function toggleProductSelection($index)
    {
        $this->productsForOrder[$index]['selected'] = !$this->productsForOrder[$index]['selected'];
    }


    public function updateProductQuantity($index, $quantity)
    {
        $this->productsForOrder[$index]['expected_quantity'] = max((int) $quantity, 0);
    }


    public function filterData(): void
    {
        if (empty($this->producer) || empty($this->producer->id)) {
            $this->productsForOrder = [];
            return;
        }

        if (empty($this->searchTerm) || !is_string($this->searchTerm)) {
            $this->productsForOrder = $this->getProductsForOrder($this->producer->id);
            return;
        }

        $searchTerm = trim(mb_strtolower($this->searchTerm));
        $this->productsForOrder = array_values(array_filter($this->productsForOrder, function ($product) use ($searchTerm) {
            return stripos(mb_strtolower($product['name']), $searchTerm) !== false
                || stripos(mb_strtolower($product['reference_number']), $searchTerm) !== false;
        }));
    }


    public function generateOrder()
    {
        $selectedProducts = array_filter($this->productsForOrder, fn($product) => $product['selected'] && $product['expected_quantity'] > 0);

        if (empty($selectedProducts)) {
            Notification::make()
                ->title('Błąd')
                ->danger()
                ->body('Nie wybrano żadnych produktów lub ich ilość jest równa 0. Zaktualizuj ilości przed generowaniem zamówienia.')
                ->send();
            return;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'producer_id' => $this->producer->id,
                'status' => 'pending',
                'total_value' => 0,
            ]);

            $totalValue = collect($selectedProducts)->sum(function ($product) use ($order) {
                $productModel = Product::findOrFail($product['id']);
                $unitPrice = $productModel->wholesale_price ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['expected_quantity'],
                    'unit_price' => $unitPrice,
                ]);

                $productModel->decrement('stock_available', $product['expected_quantity']);
                return $product['expected_quantity'] * $unitPrice;
            });

            $order->update(['total_value' => $totalValue]);

            foreach (['pdf' => 'generatePdfFile', 'xls' => 'generateExcelFile'] as $key => $method) {
                if ($filePath = $this->$method($order, $selectedProducts)) {
                    $order->update(["{$key}_file" => $filePath]);
                }
            }

            DB::commit();
            Notification::make()->title('Zamówienie wygenerowane')->success()->body('Zamówienie zostało pomyślnie wygenerowane.')->send();
            return redirect()->to(OrderResource::getUrl('details', ['record' => $order->id]));
        } catch (Exception $e) {
            DB::rollBack();
            Notification::make()->title('Błąd')->danger()->body('Wystąpił problem: ' . $e->getMessage())->send();
            return;
        }
    }



    public function generatePdfFile(Order $order)
    {
        try {
            if (!$order || !$order->producer || !$order->producer->name) {
                Log::warning('Nie można wygenerować PDF - brak zamówienia lub producenta.');
                return false;
            }

            $orderItems = $order->items()->with('product')->get();

            if ($orderItems->isEmpty()) {
                Log::warning('Nie można wygenerować PDF - brak produktów w zamówieniu.');
                return false;
            }

            $pdf = Pdf::loadView('pdf.order_table', [
                'orderItems' => $orderItems,
                'producer_name' => $order->producer->name,
                'order' => $order,
            ]);

            $fileName = Str::slug($order->producer->name) . '_order_' . now()->format('Y_m_d_H_i_s') . '.pdf';
            $path = 'pdf/orders/' . $fileName;
            Storage::disk('public')->makeDirectory('pdf/orders');
            $pdf->save(storage_path('app/public/' . $path));

            if (!Storage::disk('public')->exists($path)) {
                Log::error('Błąd zapisu pliku PDF: ' . $path);
                return false;
            }

            return $path;
        } catch (Exception $e) {
            Log::error('Błąd generowania PDF: ' . $e->getMessage());
            return false;
        }
    }



    public function generateExcelFile(Order $order)
    {
        try {
            if (!$order->exists || !$order->producer?->name) {
                Log::warning('Nie można wygenerować pliku Excel - brak zamówienia lub producenta.');
                return false;
            }

            $orderItems = $order->items()->with('product')->get();

            if ($orderItems->isEmpty()) {
                Log::warning('Nie można wygenerować pliku Excel - brak produktów w zamówieniu.');
                return false;
            }

            $fileName = Str::slug($order->producer->name) . '_order_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filePath = 'excel/orders/' . $fileName;

            Storage::disk('public')->makeDirectory('excel/orders');

            Excel::store(new OrderExport($order), $filePath, 'public');

            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('Błąd zapisu pliku Excel: ' . $filePath);
                return false;
            }

            return $filePath;
        } catch (Exception $e) {
            Log::error('Błąd generowania pliku Excel: ' . $e->getMessage());
            return false;
        }
    }



}
