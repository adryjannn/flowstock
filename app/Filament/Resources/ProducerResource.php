<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProducerResource\Pages;
use App\Filament\Resources\ProducerResource\RelationManagers;
use App\Models\Producer;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;


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
                Section::make(__('Required*'))->schema([
                    TextInput::make('name')
                        ->required()
                        ->label(__('Name'))
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        ->nullable()
                        ->label(__('Phone'))
                        ->maxLength(20),

                    TextInput::make('email')
                        ->nullable()
                        ->email()
                        ->label(__('Email'))
                        ->maxLength(255),

                    TextInput::make('delivery_time')
                        ->required()
                        ->label(__('Order processing time'))
                        ->integer()
                        ->minValue(1)
                        ->helperText(__('Number of days to process the order')),


                    TextInput::make('minimum_order_value')
                        ->nullable()
                        ->label(__('Minimum order value'))
                        ->minValue(1)

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
                TextColumn::make('id')
                    ->searchable()
                    ->sortable()
                    ->label(__('ID')),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('Name')),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('Email')),

                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->label(__('Phone')),

                TextColumn::make('delivery_time')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('Delivery time')),

                TextColumn::make('minimum_order_value')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('Logistic minimum')),

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
