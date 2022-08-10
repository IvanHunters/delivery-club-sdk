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
            "/v2/stores/".$this->store."/catalog/categories",
            [
                RequestOptions::JSON => [
                    "is_discount" => false,
                    "categoriesId" => $categories
                ]
            ]
        );

        return $this;
    }

    public function getProducts(): array
    {
        $catalog = $this->catalog['categories'];
        $categoryName = $catalog[0]['name'];

        $subcategories = array_column($catalog, "subcategories")[0];
        $products = [];
        foreach ($subcategories as $subcategory)
        {
            $subcategoryName = $subcategory['name'];
            $productsData = $subcategory["products"];
            $productItems = $productsData["items"];
            $productDataItems = self::mappingProductItem(is_null($productItems) ? []: $productItems, $categoryName, $subcategoryName);

            $discountProducts = $subcategory["discountProducts"];
            $discountProductItems = $discountProducts["items"];
            $discountItems = self::mappingProductItem(is_null($discountProductItems) ? []: $discountProductItems, $categoryName, $subcategoryName);

            $products = $products + $discountItems + $productDataItems;
        }
        return $products;
    }

    public static function mappingProductItem(array $items, string $categoryName = "", string $subcategoryName = "")
    {
        $mappedItems = [];
        foreach ($items as $item)
        {
            $productId = $item['id'];
            $item['price'] = isset($item['discountPrice']) ? $item['discountPrice'] : $item['price'];
            $item['imageUrl'] = "https://www.delivery-club.ru" .$item['imageUrl'];
            $item['category'] = $categoryName;
            $item['subCategory'] = $subcategoryName;
            $mappedItems[$productId] = $item;
        }

        return $mappedItems;
    }

}