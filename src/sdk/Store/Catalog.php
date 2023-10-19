<?php

declare(strict_types=1);

namespace Lichi\Delivery\Sdk\Store;

use GuzzleHttp\RequestOptions;
use Lichi\Delivery\ApiProvider;
use Lichi\Delivery\Sdk\Module;

class Catalog extends Module
{
    private string $store;
    private array $catalog = [];

    public function __construct(ApiProvider $provider, string $store)
    {
        $this->store = $store;
        parent::__construct($provider);
    }

    public function get(array $categories): self
    {
        $this->catalog = $this->apiProvider->callMethod(
            "POST",
            "/api/v2/menu/goods/get-categories?auto_translate=false",
            [
                "headers" => ["X-Device-Id" => 'tefsdf'],
                RequestOptions::JSON => [
                    "slug" => $this->store,
                    "categories" => $this->getCategoriesList($categories)
                ]
            ]
        );

        return $this;
    }

    private function getCategoriesList(array $categories): array
    {
        $return = [];
        foreach($categories as $category) {
            $return[] = [
                "id" => $category,
                "min_items_count" => 1,
                "max_items_count" => 1000000
            ];
        }
        return $return;
    }

    public function getProducts(): array
    {
        $catalogs = $this->catalog['categories'];
        $products = [];

        foreach ($catalogs as $catalog) {
            $categoryName = $catalog['name'];
            $items = $catalog['items'];
            foreach ($items as $item) {
                $products[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'weight' => $item['weight'] ?? 0,
                    'category' => $categoryName,
                    'imageUrl' => $item['picture']['url'],
                    'price' => !is_null($item['promoPrice']) ? $item['promoPrice'] : $item['price'],
                ];
            }
        }
        return $products;
    }

}