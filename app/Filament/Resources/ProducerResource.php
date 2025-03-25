<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProducerResource\Pages;
use App\Filament\Resources\ProducerResource\RelationManagers;
use App\Models\Producer;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;


class ProducerResource extends Resource
{
    protected static ?string $model = Producer::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationLabel(): string
    {
        return 'Producenci';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Katalog';
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Dane podstawowe'))
                    ->description(__('Podstawowe informacje o producencie'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Nazwa producenta'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('Wpisz nazwę producenta')),

                        TextInput::make('phone')
                            ->label(__('Telefon kontaktowy'))
                            ->tel()
                            ->telRegex('/^\+?[0-9\s\-\(\)]{7,20}$/')
                            ->nullable()
                            ->maxLength(20)
                            ->placeholder(__('Podaj numer telefonu'))
                            ->helperText(__('Numer telefonu w formacie międzynarodowym')),

                        TextInput::make('email')
                            ->label(__('Adres e-mail'))
                            ->nullable()
                            ->email()
                            ->maxLength(255)
                            ->placeholder(__('Wpisz adres e-mail'))
                            ->helperText(__('Oficjalny adres e-mail producenta')),
                    ])
                    ->columns(2)
                    ->columnSpan(2),

                Section::make(__('Warunki współpracy'))
                    ->description(__('Dodatkowe warunki związane z dostawami'))
                    ->schema([
                        TextInput::make('delivery_time')
                            ->label(__('Czas przetwarzania zamówienia'))
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->placeholder(__('Podaj liczbę dni'))
                            ->helperText(__('Liczba dni potrzebna na realizację zamówienia')),

                        TextInput::make('minimum_order_value')
                            ->label(__('Minimalna wartość zamówienia'))
                            ->nullable()
                            ->numeric()
                            ->minValue(1)
                            ->prefix('PLN')
                            ->placeholder(__('Podaj minimalną wartość'))
                            ->helperText(__('Minimalna wartość zamówienia w złotówkach')),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('Nazwa producenta'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('Adres e-mail'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Telefon kontaktowy'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('delivery_time')
                    ->label(__('Czas realizacji'))
                    ->sortable()
                    ->searchable()
                    ->suffix(__(' dni'))
                    ->toggleable(),

                TextColumn::make('minimum_order_value')
                    ->label(__('Minimalna wartość zamówienia'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->prefix('PLN'),
            ])
            ->filters([
                Filter::make('minimum_order_value_range')
                    ->form([
                        TextInput::make('min_value')
                            ->label(__('Minimalna wartość zamówienia od'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('PLN'),

                        TextInput::make('max_value')
                            ->label(__('Minimalna wartość zamówienia do'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('PLN'),
                    ])
                    ->query(fn ($query, $data) => $query
                        ->when($data['min_value'], fn ($q) => $q->where('minimum_order_value', '>=', $data['min_value']))
                        ->when($data['max_value'], fn ($q) => $q->where('minimum_order_value', '<=', $data['max_value']))
                    ),

                Filter::make('delivery_time_range')
                    ->form([
                        TextInput::make('min_days')
                            ->label(__('Czas realizacji od'))
                            ->numeric()
                            ->minValue(1)
                            ->suffix(__(' dni')),

                        TextInput::make('max_days')
                            ->label(__('Czas realizacji do'))
                            ->numeric()
                            ->minValue(1)
                            ->suffix(__(' dni')),
                    ])
                    ->query(fn ($query, $data) => $query
                        ->when($data['min_days'], fn ($q) => $q->where('delivery_time', '>=', $data['min_days']))
                        ->when($data['max_days'], fn ($q) => $q->where('delivery_time', '<=', $data['max_days']))
                    ),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edytuj')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Usuń zaznaczone')),
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
