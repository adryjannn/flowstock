<?php

namespace App\Filament\Resources\OrderStateResource\Pages;

use App\Filament\Resources\OrderStateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderState extends EditRecord
{
    protected static string $resource = OrderStateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
