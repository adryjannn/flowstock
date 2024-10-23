<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\OrderState;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationLabel(): string
    {
        return 'Lista zamówień';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Zamówienia';
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Możesz dodać pola formularza tutaj, jeśli są potrzebne
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('producer.name')
                    ->label('Producent')
                    ->sortable()
                    ->searchable(),
                SelectColumn::make('status')  // SelectColumn dla statusu
                ->label('Status')
                    ->options(OrderState::all()->pluck('name', 'id')) // Pobranie statusów z OrderState
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_value')
                    ->label('Wartość')
                    ->money('PLN')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordUrl(function ($record) {
                if (!$record->id) {
                    return null;
                }

                return static::getUrl('details', ['record' => $record->id]);
            })
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'details' => Pages\OrderDetails::route('/{record}/details')
        ];
    }

    public static function getCustomUrl($id)
    {
        return static::getUrl('details', ['record' => $id]);
    }
}
