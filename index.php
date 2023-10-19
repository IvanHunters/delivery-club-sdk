<?php

include "vendor/autoload.php";
use GuzzleHttp\Client;
use Lichi\Delivery\ApiProvider;

$client = new Client([
    'base_uri' => "https://market-delivery.yandex.ru",
    'verify' => false,
    'timeout'  => 30.0,
]);

$apiProvider = new ApiProvider($client);
$stores = $apiProvider->store(48.69693, 44.493523)->get()->getStores();

//$stores = array (
//  'flawery_phmzo' => 'Flawery',
//  'lenta_bvjci' => 'Гипер Лента',
//  'vkusvill_rabochekrestyanskaya_31_yfgok' => 'ВкусВилл Экспресс',
//  'vkusvill_kozlovskaya_44a_chayp' => 'ВкусВилл Гипер',
//  'ozerki_bmhwy' => 'Озерки',
//  'magnit_celevaya_gwtjl' => 'Магнит',
//  'magnit_kosmetik_celevaya_5fcnp' => 'Магнит Косметик',
//  'doktor_stoletov_zsqnt' => 'Доктор Столетов',
//  'chetyre_lapy_mnvbt' => 'Четыре Лапы',
//  'superapteka_hiybd' => 'Супераптека',
//  'mpr_78tz2' => 'МПР',
//  'magnit_semejnyj_celevaya_7n5s6' => 'Магнит Семейный',
//  'fix_q8gkh' => 'Fix Price',
//);

foreach ($stores as $store)
{

    $storeId = $store['id'];
    $title = $store['title'];
    $categories = $apiProvider->category($storeId)->get();
    $categoryIds = array_column($categories, "id");
    $catalog = [];
    foreach ($categoryIds as $categoryId)
    {
        $products = $apiProvider->catalog($storeId)->get([$categoryId])->getProducts();
        $catalog = array_merge($catalog, $products);
    }

//    $a = 10
//    file_put_contents("skt/$title.json", json_encode($catalog, JSON_UNESCAPED_UNICODE));
}

$a = 10;