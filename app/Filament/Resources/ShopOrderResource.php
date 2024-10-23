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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_shop_order')
                    ->label('Shop Order ID')
                    ->required(),
                Forms\Components\TextInput::make('order_reference')
                    ->label('Order Reference')
                    ->required(),
                Forms\Components\Select::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                        'bank_transfer' => 'Bank Transfer',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('carrier')
                    ->label('Carrier')
                    ->required(),
                Forms\Components\Select::make('order_state')
                    ->label('Order State')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('total_paid')
                    ->label('Total Paid')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('total_shipping')
                    ->label('Total Shipping')
                    ->numeric()
                    ->required(),
                Forms\Components\DatePicker::make('created_at')
                    ->label('Created At')
                    ->disabled(),
                Forms\Components\DatePicker::make('updated_at')
                    ->label('Updated At')
                    ->disabled(),
            ]);
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
                    ->label('ID'),
                TextColumn::make('id_shop_order')
                    ->sortable()
                    ->label('Shop Order ID'),
                TextColumn::make('order_reference')
                    ->sortable()
                    ->label('Order Reference'),
                TextColumn::make('payment_type')
                    ->sortable()
                    ->label('Payment Type')
                    ->toggleable(),
                TextColumn::make('carrier')
                    ->sortable()
                    ->label('Carrier'),
                TextColumn::make('order_state')
                    ->sortable()
                    ->label('Order State'),
                TextColumn::make('total_paid')
                    ->sortable()
                    ->label('Total paid'),
                TextColumn::make('total_shipping')
                    ->sortable()
                    ->label('Total shipping')
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('details')
                    ->label(__('Details'))
                    ->url(fn (ShopOrder $record): string => static::getUrl('details', ['record' => $record->id]))
                    ->icon('heroicon-o-eye')
                    ->color('success'),
                Action::make('edit')
                    ->label(__('Edit'))
                    ->url(fn (ShopOrder $record): string => static::getUrl('edit', ['record' => $record->id]))
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
