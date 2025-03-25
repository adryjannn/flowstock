<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\ShopOrderStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncPrestashopService
{
    protected $apiUrl;
    protected $apiKey;
    protected $moduleUrl;

    public function __construct()
    {
        $baseUrl = rtrim(Configuration::where('key', 'PRESTASHOP_URL')->value('value'), '/');
        $this->apiUrl = "{$baseUrl}/api";
        $this->moduleUrl = "{$baseUrl}/module/flowstock";
        $this->apiKey = Configuration::where('key', 'PRESTASHOP_API_KEY')->value('value');

        if (!$this->apiUrl || !$this->apiKey) {
            throw new \Exception('PrestaShop configuration is missing.');
        }
    }

    protected function fetchData($endpoint)
    {
        $response = Http::withBasicAuth($this->apiKey, '')->withHeaders([
            'Output-Format' => 'JSON'
        ])->get("{$this->apiUrl}/{$endpoint}");

        if ($response->successful()) {
            return json_decode($response->body(), true);
        }

        throw new \Exception("Failed to fetch data from endpoint: {$endpoint}. Error: " . $response->body());
    }

    protected function fetchDetails($endpoint, $id)
    {
        $response = Http::withBasicAuth($this->apiKey, '')->withHeaders([
            'Output-Format' => 'JSON'
        ])->get("{$this->apiUrl}/{$endpoint}/{$id}");

        if ($response->successful()) {
            return json_decode($response->body(), true);
        }

        throw new \Exception("Failed to fetch details for ID: {$id} from endpoint: {$endpoint}. Error: " . $response->body());
    }



    public function getOrderStatuses()
    {
        $jsonObject = $this->fetchData('order_states');
        return $this->parseItems($jsonObject['order_states'], 'order_state', 'getOrderStateDetails');
    }

    protected function getOrderStateDetails($id)
    {
        $jsonObject = $this->fetchDetails('order_states', $id);
        $name = $jsonObject['order_state']['name'] ?? '';

        return [
            'id' => (int) $jsonObject['order_state']['id'],
            'name' => $name,
        ];
    }


    protected function parseItems($items, $itemName, $detailsMethod)
    {
        $parsedItems = [];
        foreach ($items as $item) {
            $itemDetails = $this->$detailsMethod((int) $item['id']);
            $parsedItem = [
                'id' => $itemDetails['id'],
                'name' => $itemDetails['name'],
            ];
            if (isset($itemDetails['email'])) {
                $parsedItem['email'] = $itemDetails['email'];
            }

            if (isset($itemDetails['color'])) {
                $parsedItem['color'] = $itemDetails['color'];
            }

            $parsedItems[] = $parsedItem;
        }
        return $parsedItems;
    }

    public function syncAllData()
    {
        $this->syncData('order_states', ShopOrderStatus::class, 'getOrderStatuses', 'shop_order_status_id');
    }

    protected function syncData($dataType, $modelClass, $serviceMethod, $identifier)
    {
        try {
            $data = call_user_func([$this, $serviceMethod]);
            foreach ($data as $item) {
                $attributes = [
                    'name' => $item['name'],
                ];
                if (isset($item['email'])) {
                    $attributes['email'] = $item['email'];
                }

                if (isset($item['color'])) {
                    $attributes['color'] = $item['color'];
                }

                $modelClass::updateOrCreate(
                    [$identifier => $item['id']],
                    $attributes
                );
            }
        } catch (\Exception $e) {
            throw new \Exception("Failed to synchronize $dataType: " . $e->getMessage());
        }
    }

    public static function checkConnection()
    {
        try {
            $apiUrl = rtrim(Configuration::where('key', 'PRESTASHOP_URL')->value('value'), '/') . '/api';
            $apiKey = Configuration::where('key', 'PRESTASHOP_API_KEY')->value('value');

            if (!$apiUrl || !$apiKey) {
                return false;
            }

            $response = Http::withBasicAuth($apiKey, '')->withHeaders([
                'Output-Format' => 'JSON'
            ])->get("{$apiUrl}/manufacturers");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateAdminOrderUrl($id_order)
    {
        $url = "{$this->moduleUrl}/adminlink?id_order={$id_order}";

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            $link = json_decode($response->body(), true);
            return $link;
        }

        throw new \Exception('Failed to generate admin order URL.');
    }

    public function changeOrderState($id_order, $id_order_state)
    {
        $url = "{$this->moduleUrl}/updatestatus?id_order={$id_order}&id_order_state={$id_order_state}";

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($url);

        Log::info($url . ' ' . $response->successful());
        return $response->json();
    }
}
