<?php

declare(strict_types=1);

namespace Lichi\Delivery\Sdk\Store;

use GuzzleHttp\RequestOptions;
use Lichi\Delivery\ApiProvider;
use Lichi\Delivery\Sdk\Module;

class Store extends Module
{
    private float $latitude;
    private float $longitude;
    private array $stores = [];

    public function __construct(ApiProvider $provider, float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        parent::__construct($provider);
    }

    public function get(): self
    {
        $this->stores = $this->apiProvider->callMethod(
            "POST",
            "/eats/v1/layout-constructor/v1/layout",
            [
                "headers" => ["X-Device-Id" => 'tefsdf'],
                "json" => [
                    "location" => [
                        "latitude" => $this->latitude,
                        "longitude" => $this->longitude
                    ],
                    "view" => [
                        "type" => "collection",
                        "slug" => "shops"
                    ]
                ]
            ]);
        return $this;
    }

    public function getStoreInfo(string $store): array
    {
        $url = "/api/v2/catalog/" .$store. "?longitude=" .$this->longitude. "&latitude=" .$this->latitude. "&shippingType=delivery";
        return $this->apiProvider->callMethod(
            "GET",
            $url
        )['payload'];
    }

    public function getStores(): array
    {
        $storesInfo = [];
        $stores = $this->stores;
        $storesData = $stores['data'] ?? [];
        $storesPlaces = $storesData['places_lists'] ?? [];
        foreach ($storesPlaces as $storesPlace)
        {
            $payloadPlaces = $storesPlace['payload']['places'];
            foreach ($payloadPlaces as $payloadPlace) {
                $storesId = $payloadPlace['slug'];
                $storeName = $payloadPlace['name'];
                $storesInfo[] = [
                    "id" => $storesId,
                    "title" => $storeName,
                    "address" => $this->getStoresAddresses($storesId)
                ];
            }
        }
        return $storesInfo;
    }


    private function getStoresAddresses(string $storeName): array
    {
        $data = $this->getStoreInfo($storeName);
        $place = $data['foundPlace']['place'] ?? [];

        return $place['address'] ?? [];
    }

}