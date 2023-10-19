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

    public function getStores(): array
    {
        $storesInfo = [];
        $stores = $this->stores;
        $storesData = $stores['data'];
        $storesPlaces = $storesData['places_lists'];
        foreach ($storesPlaces as $storesPlace)
        {
            $payloadPlaces = $storesPlace['payload']['places'];
            foreach ($payloadPlaces as $payloadPlace) {
                $storesId = $payloadPlace['slug'];
                $storeName = $payloadPlace['name'];
                $storesInfo[$storesId] = $storeName;
            }
        }
        return $storesInfo;
    }


    private function getStoresInfo(array $placeholders)
    {
        $placeholdersStores = $placeholders[0][1]['placeholders'];
        $placeholdersOption = array_column($placeholdersStores, 'placeholders')[0];
        return array_column($placeholdersOption, 'data');
    }


    private function getStoresAddresses(array $placeholders)
    {
        $addresses = [];
        foreach ($placeholders as $placeholder)
        {
            $info = $placeholder[1];
            $placeholderData = $info['placeholders'];
            $placeholdersOption = array_column($placeholderData, 'placeholders')[0];
            $placeholdersOptionPlaceholder = array_column($placeholdersOption, 'data')[0];
            $addresses[] = $placeholdersOptionPlaceholder;
        }
        return $addresses;
    }

}