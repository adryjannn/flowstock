<?php

namespace App\Filament\Resources\ShopOrderResource\Pages;

use App\Filament\Resources\ShopOrderResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class EditShopOrder extends EditRecord
{
    protected static string $resource = ShopOrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Add any pre-save logic here if necessary
        return $data;
    }

    protected function afterSave(): void
    {
        // Prepare the data to be sent to the PrestaShop API
        $orderData = [
            'id' => $this->record->id,
            'current_state' => 1,
        ];

        // Convert the data to XML
        $xmlString = $this->convertArrayToXml($orderData);


        $apiKey = 'AQ1WZ5HZDTUSR3ES4SIHZ2DC9L1IEQXL';
        $endpointUrl = "https://pbogthdech.cfolks.pl/api/orders/{$this->record->id}?ws_key=$apiKey";

        $response = Http::withHeaders([
            'Content-Type' => 'application/xml',
        ])->put($endpointUrl, $xmlString);



        if ($response->successful()) {
            Notification::make()
                ->title('Order updated successfully in PrestaShop')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to update order in PrestaShop')
                ->danger()
                ->send();
        }
    }

    protected function convertArrayToXml(array $data): string
    {
        $xml = new \SimpleXMLElement('<prestashop/>');
        $order = $xml->addChild('order');
        $order->addChild('id', $data['id']);
        $order->addChild('current_state', $data['current_state']);
        // Add any other required fields here
        return $xml->asXML();
    }
}
