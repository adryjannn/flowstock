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
use Illuminate\Support\Facades\Redirect;
use Exception;
use Illuminate\Support\Facades\Storage;
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

    public function getProductsForOrder($producerId)
    {
        return Product::where('producer_id', $producerId)
            ->leftJoin('shop_orders_product', 'products.reference_number', '=', 'shop_orders_product.product_code')
            ->select('products.*', DB::raw('COALESCE(SUM(shop_orders_product.quantity), 0) as sold_quantity'))
            ->groupBy('products.id')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'reference_number' => $product->reference_number,
                    'stock_available' => $product->stock_available,
                    'sold_quantity' => $product->sold_quantity,
                    'expected_quantity' => 0,
                    'selected' => false,
                ];
            })->toArray();
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


    public function filterData()
    {
        if (!empty($this->searchTerm)) {
            $this->productsForOrder = array_filter($this->productsForOrder, function ($product) {
                return stripos($product['name'], $this->searchTerm) !== false || stripos($product['reference_number'], $this->searchTerm) !== false;
            });
        } else {
            $this->productsForOrder = $this->getProductsForOrder($this->producer->id);
        }
    }

    public function generateOrder()
    {
        $selectedProducts = array_filter($this->productsForOrder, function ($product) {
            return $product['selected'] === true && $product['expected_quantity'] > 0;
        });

        $productsWithZeroQuantity = array_filter($this->productsForOrder, function ($product) {
            return $product['selected'] === true && $product['expected_quantity'] <= 0;
        });

        if (count($productsWithZeroQuantity) > 0) {
            Notification::make()
                ->title('Błąd ilości')
                ->danger()
                ->body('Niektóre zaznaczone produkty mają ilość równą 0. Zaktualizuj ilości przed generowaniem zamówienia.')
                ->send();
            return;
        }

        if (count($selectedProducts) === 0) {
            Notification::make()
                ->title('Brak wybranych produktów')
                ->warning()
                ->body('Nie wybrano żadnych produktów do zamówienia.')
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'producer_id' => $this->producer->id,
                'note' => '',
                'status' => 'pending',
                'total_value' => 0,
            ]);

            $totalValue = 0;

            foreach ($selectedProducts as $product) {
                $productModel = Product::find($product['id']);
                if (!$productModel) {
                    throw new Exception("Produkt o ID {$product['id']} nie został znaleziony.");
                }

                $unitPrice = $productModel->unit_price ?? 0;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['expected_quantity'],
                    'unit_price' => $unitPrice,
                ]);

                $totalValue += $orderItem->quantity * $orderItem->unit_price;
                $productModel->decrement('stock_available', $orderItem->quantity);
            }

            $order->update(['total_value' => $totalValue]);


            $pdfFilePath = $this->generatePdfFile($order, $selectedProducts);
            $excelFilePath = $this->generateExcelFile($order, $selectedProducts);

            if ($pdfFilePath && $excelFilePath) {
                $order->update([
                    'pdf_file' => $pdfFilePath,
                    'xls_file' => $excelFilePath,
                ]);
            } else {

                Notification::make()
                    ->title('Zamówienie wygenerowane, ale nie udało się utworzyć plików PDF lub Excel.')
                    ->warning()
                    ->body('Zamówienie zostało pomyślnie utworzone, ale wystąpiły problemy z generowaniem plików.')
                    ->send();
            }


            DB::commit();

            Notification::make()
                ->title('Zamówienie wygenerowane')
                ->success()
                ->body('Zamówienie zostało pomyślnie wygenerowane.')
                ->send();

            return redirect()->to(OrderResource::getUrl('details', ['record' => $order->id]));
        } catch (Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Błąd')
                ->danger()
                ->body('Wystąpił problem podczas generowania zamówienia: ' . $e->getMessage())
                ->send();
            return;
        }
    }


    public function generatePdfFile(Order $order, $selectedProducts)
    {
        $pdf = Pdf::loadView('pdf.order_table', [
            'productsForOrder' => $selectedProducts,
            'producer_name' => $this->producer->name,
            'order' => $order,
        ]);

        $fileName = strtolower(str_replace(" ", "_", $this->producer->name)) . '_order_' . now()->format('Y_m_d_H_i_s') . '.pdf';
        $path = 'pdf/orders/' . $fileName;

        if (!Storage::disk('public')->exists('pdf/orders')) {
            Storage::disk('public')->makeDirectory('pdf/orders');
        }
        $pdf->save(storage_path('app/public/' . $path));

        return Storage::disk('public')->exists($path) ? $path : false;
    }



    public function generateExcelFile(Order $order, $selectedProducts)
    {
        $baseFileName = strtolower(str_replace(" ", "_", $this->producer->name)) . '_order_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $filePath = 'excel/orders/' . $baseFileName;
        $export = new OrderExport($selectedProducts);

        if (!Storage::disk('public')->exists('excel/orders')) {
            Storage::disk('public')->makeDirectory('excel/orders');
        }

        Excel::store($export, $filePath, 'public');

        return Storage::disk('public')->exists($filePath) ? $filePath : false;
    }

}
