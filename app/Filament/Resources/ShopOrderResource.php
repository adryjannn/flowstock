<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopOrderResource\Pages;
use App\Filament\Resources\ShopOrderResource\RelationManagers;
use App\Models\ShopOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopOrderResource extends Resource
{
    protected static ?string $model = ShopOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Zamówienia ze sklepu';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Katalog';
    }
    public static function canCreate(): bool
    {
        return false;
    }


    public static function getNavigationBadge(): ?string
    {
        $today = \Carbon\Carbon::today();
        return static::getModel()::whereDate('created_at', $today)->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->label(__('ID')),

                TextColumn::make('id_shop_order')
                    ->sortable()
                    ->label(__('ID zamówienia w sklepie')),

                TextColumn::make('order_reference')
                    ->sortable()
                    ->label(__('Numer referencyjny zamówienia')),

                TextColumn::make('payment_type')
                    ->sortable()
                    ->toggleable()
                    ->label(__('Typ płatności')),

                TextColumn::make('carrier')
                    ->sortable()
                    ->label(__('Przewoźnik')),

                TextColumn::make('orderStatus.name')
                    ->sortable()
                    ->label(__('Status zamówienia'))
                    ->badge() // Dodaje kolorowe oznaczenie statusu

                    ->color(fn ($record) => match ($record->orderStatus->name ?? '') {
                        'Oczekujące' => 'warning',
                        'Zrealizowane' => 'success',
                        'Anulowane' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_paid')
                    ->sortable()
                    ->label(__('Łączna kwota'))
                    ->prefix('PLN')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' ') . ' zł'),

                TextColumn::make('total_shipping')
                    ->sortable()
                    ->label(__('Koszt wysyłki'))
                    ->prefix('PLN')
                    ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' ') . ' zł'),
            ])
            ->filters([
                //
            ])
            ->recordUrl(function ($record) {
                if (!$record->id) {
                    return null;
                }
                return static::getUrl('details', ['record' => $record->id]);
            })
            ->actions([
                Action::make('details')
                    ->label(__('Szczegóły'))
                    ->url(fn (ShopOrder $record): string => static::getUrl('details', ['record' => $record->id]))
                    ->icon('heroicon-o-eye')
                    ->color('success'),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Usuń zaznaczone')),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShopOrders::route('/'),
            'create' => Pages\CreateShopOrder::route('/create'),
            'edit' => Pages\EditShopOrder::route('/{record}/edit'),
            'details' => Pages\ShopOrderDetails::route('/{record}/details')
        ];
    }
}
