<?php

namespace App\Filament\Pages;

use App\Models\Producer; // Model producenta
use App\Models\ShopOrderProduct; // Model ShopOrderProduct
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class PotentialOrders extends Page implements Tables\Contracts\HasTable
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.potential-orders';

    public static function getNavigationLabel(): string
    {
        return 'Stwórz zamówienie';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Zamówienia';
    }

    use Tables\Concerns\InteractsWithTable;

    protected function getTableQuery(): Builder
    {
        return Producer::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label('ID')
                ->sortable(),
            TextColumn::make('name')
                ->label('Nazwa Producenta')
                ->sortable()
                ->searchable()
                ->action(function ($record) {
                    return redirect()->to(
                        CreateOrderToProducer::getUrl([
                            'producerId' => $record->id,
                            'daysCount' => 30,
                        ])
                    );
            }),

            TextColumn::make('total_sold_value')
                ->label('Łączna Wartość Sprzedanych Produktów')
                ->getStateUsing(function ($record) {
                    return ShopOrderProduct::query()
                        ->join('products', 'shop_orders_product.product_code', '=', 'products.reference_number')
                        ->where('products.producer_id', $record->id)
                        ->sum(\DB::raw('shop_orders_product.quantity * shop_orders_product.product_price'));
                })
                ->formatStateUsing(fn ($state) => number_format($state, 2) . ' PLN'), // Formatowanie do dwóch miejsc po przecinku
        ];
    }
}
