<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProducerResource\Pages;
use App\Filament\Resources\ProducerResource\RelationManagers;
use App\Models\Producer;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;

class ProducerResource extends Resource
{
    const CURRENCIES = [
        'PLN' => 'PLN',
        'EUR' => 'EUR',
        'USD' => 'USD',
        'CNY' => 'CNY'
    ];
    protected static ?string $model = Producer::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return __('Producers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Required*'))->schema([
                    TextInput::make('name')->required()->label(__('Name')),
                    TextInput::make('full_name')->nullable()->label(__('Full name')),
                    TextInput::make('phone')->nullable()->label(__('Phone')),
                    TextInput::make('email')->nullable()->email()->label(__('Email')),
                    TextInput::make('delivery_time')->required()->label(__('Order processing time'))->integer(),
                    TextInput::make('logistic_minimum')->nullable()->label(__('Logistic minimum net')),
                    TextInput::make('time_in_stock_max')->required()->label(__('Time in stock max'))->integer(),
                    TextInput::make('time_in_stock_min')->required()->label(__('Time in stock min'))->integer(),
                ])->columns(2)->columnSpan(2)
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable()->label(__('ID')),
                TextColumn::make('name')->searchable()->sortable()->label(__('Name')),
                TextColumn::make('full_name')->searchable()->sortable()->toggleable()->label(__('Full name')),
                TextColumn::make('email')->searchable()->sortable()->toggleable()->toggleable()->label(__('Email')),
                TextColumn::make('phone')->searchable()->toggleable()->label(__('Phone')),
                TextColumn::make('delivery_time')->searchable()->sortable()->toggleable()->label(__('Delivery time')),
                TextColumn::make('logistic_minimum')->searchable()->sortable()->toggleable()->label(__('Logistic minimum')),
                TextColumn::make('currency')->searchable()->toggleable()->label(__('Currency')),
            ])
            ->filters([

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
            'index' => Pages\ListProducers::route('/'),
            'create' => Pages\CreateProducer::route('/create'),
            'edit' => Pages\EditProducer::route('/{record}/edit'),
        ];
    }
}
