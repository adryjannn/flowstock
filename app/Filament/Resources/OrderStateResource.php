<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderStateResource\Pages;
use App\Filament\Resources\OrderStateResource\RelationManagers;
use App\Models\OrderState;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderStateResource extends Resource
{
    protected static ?string $model = OrderState::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Statusy zamówień';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Zamówienia';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color')
                ->label('Kolor Statusu')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                ColorColumn::make('color')
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
            'index' => Pages\ListOrderStates::route('/'),
            'create' => Pages\CreateOrderState::route('/create'),
            'edit' => Pages\EditOrderState::route('/{record}/edit'),
        ];
    }
}
