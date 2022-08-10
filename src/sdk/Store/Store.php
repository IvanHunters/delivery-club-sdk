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
            "GET",
            sprintf("/v2/catalog/stores/?category_ids=3,4,5,6,7&lat=%s&long=%s&with_blocked=true", $this->latitude, $this->longitude)
        );
        return $this;
    }

    public function getStores(): array
    {
        $storesData = $this->stores;
        $groups = $storesData['groups'];
        $groupStores = [];
        foreach ($groups as $group)
        {
            $stores = $group['stores'];
            $groupStores = array_merge($groupStores, $stores);
        }
        return $groupStores;
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