<?php

declare(strict_types=1);

namespace Lichi\Delivery\Sdk\Store;

use GuzzleHttp\RequestOptions;
use Lichi\Delivery\ApiProvider;
use Lichi\Delivery\Sdk\Module;

class Category extends Module
{
    private string $store;

    public function __construct(ApiProvider $provider, string $store)
    {
        $this->store = $store;
        parent::__construct($provider);
    }


    public function get(): array
    {
        return $this->apiProvider->callMethod(
            "GET",
            "/v2/stores/".$this->store."/catalog/main"
        )['categories'];
    }


}