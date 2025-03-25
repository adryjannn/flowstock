<?php

namespace App\Filament\Resources\OrderStateResource\Pages;

use App\Filament\Resources\OrderStateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderStates extends ListRecords
{
    protected static string $resource = OrderStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
