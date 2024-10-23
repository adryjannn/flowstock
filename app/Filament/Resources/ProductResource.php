<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Produkty';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Katalog';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
                TextInput::make('reference_number')
                    ->label(__('Reference Number'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('Description')),
                TextInput::make('stock_available')
                    ->label(__('Stock Available'))
                    ->numeric()
                    ->required(),
                Select::make('producer_id')
                    ->label(__('Producer'))
                    ->relationship('producer', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('wholesale_price')
                    ->label(__('Wholesale Price'))
                    ->numeric()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID')),
                TextColumn::make('reference_number')->label(__('Reference Number')),
                TextColumn::make('name')->label(__('Name')),
                TextColumn::make('description')->label(__('Description')),
                TextColumn::make('stock_available')->label(__('Stock Available')),
                TextColumn::make('producer.name')->label(__('Producer')),
                TextColumn::make('wholesale_price')->label(__('Wholesale Price')),
                TextColumn::make('created_at')->dateTime()->label(__('Date Added')),
                TextColumn::make('updated_at')->dateTime()->label(__('Date Updated')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
