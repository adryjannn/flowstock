<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
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
                Section::make('Informacje podstawowe')
                    ->description('Podstawowe dane produktu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nazwa produktu')
                            ->required()
                            ->placeholder('Wpisz nazwę produktu')
                            ->helperText('Pełna nazwa produktu, którą zobaczą klienci.'),

                        TextInput::make('reference_number')
                            ->label('Numer referencyjny')
                            ->required()
                            ->placeholder('Unikalny numer produktu')
                            ->helperText('Unikalny kod do identyfikacji produktu.'),
                        Textarea::make('description')
                            ->label('Opis produktu')
                            ->placeholder('Dodaj opis produktu')
                            ->helperText('Krótki opis produktu widoczny w sklepie.'),
                    ]),

                Section::make('Dostępność, producent i cena')
                    ->schema([
                        TextInput::make('stock_available')
                            ->label('Dostępna ilość')
                            ->numeric()
                            ->required()
                            ->suffix('szt.')
                            ->helperText('Ilość dostępna na magazynie.'),

                        Select::make('producer_id')
                            ->label('Producent')
                            ->relationship('producer', 'name')
                            ->searchable()
                            ->required()
                            ->placeholder('Wybierz producenta')
                            ->helperText('Producent produktu.'),
                        TextInput::make('wholesale_price')
                            ->label('Cena hurtowa')
                            ->numeric()
                            ->required()
                            ->prefix('PLN')
                            ->helperText('Cena hurtowa za jedną sztukę produktu.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('reference_number')
                    ->label('Numer referencyjny')
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nazwa produktu')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Opis produktu')
                    ->limit(50), // Skrócony opis w tabeli

                TextColumn::make('stock_available')
                    ->label('Dostępna ilość')
                    ->sortable()
                    ->suffix(' szt.'), // Dodaje jednostkę

                TextColumn::make('producer.name')
                    ->label('Producent')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('wholesale_price')
                    ->label('Cena hurtowa')
                    ->sortable()
                    ->prefix('PLN'),
            ])
            ->filters([
                SelectFilter::make('producer_id')
                    ->label('Producent')
                    ->relationship('producer', 'name')
                    ->searchable(),

                Filter::make('stock_available')
                    ->label('Dostępne produkty')
                    ->query(fn ($query) => $query->where('stock_available', '>', 0)),

            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edytuj'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Usuń zaznaczone'),
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
