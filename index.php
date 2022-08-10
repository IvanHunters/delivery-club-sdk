<?php

include "vendor/autoload.php";
use GuzzleHttp\Client;
use Lichi\Delivery\ApiProvider;

$client = new Client([
    'base_uri' => "https://mobileapi.delivery-club.ru",
    'verify' => false,
    'timeout'  => 30.0,
]);

$apiProvider = new ApiProvider($client);
$stores = $apiProvider->store(61.668797, 50.836497)->get()->getStores();

foreach ($stores as $store)
{
    $storeId = $store['id'];
    $title = $store['slug'];

    $categories = $apiProvider->category($storeId)->get();
    $categoryIds = array_column($categories, "id");
    $catalog = [];
    foreach ($categoryIds as $categoryId)
    {
        $products = $apiProvider->catalog($storeId)->get([$categoryId])->getProducts();
        $catalog = $products + $catalog;
    }

    file_put_contents("skt/$title.json", json_encode($catalog, JSON_UNESCAPED_UNICODE));
}

$a = 10;