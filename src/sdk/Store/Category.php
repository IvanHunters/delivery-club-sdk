<?php

declare(strict_types=1);

namespace Lichi\Delivery\Sdk\Store;

use GuzzleHttp\RequestOptions;
use Lichi\Delivery\ApiProvider;
use Lichi\Delivery\Sdk\Module;

class Category extends Module
{
    private string $store;
    private array $coordinates;

    public function __construct(ApiProvider $provider, string $store, array $coordinates)
    {
        $this->store = $store;
        $this->coordinates = $coordinates;
        parent::__construct($provider);
    }


    public function get(): array
    {
        $url = "/api/v2/menu/goods?auto_translate=false";
        return $this->apiProvider->callMethod(
            "POST",
                $url,
                [
                    "json" => [
                        "slug" => $this->store,
//                        "latitude" => $this->coordinates['latitude'],
//                        "longitude" => $this->coordinates['longitude'],
                        "viewed_bottomsheets" => [],
                        "maxDepth" => 0
                    ]
                ]
        )['payload']['categories'];
    }




}