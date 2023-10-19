<?php


namespace Lichi\Delivery;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Lichi\Delivery\Sdk\Store\Catalog;
use Lichi\Delivery\Sdk\Store\Category;
use Lichi\Delivery\Sdk\Store\Store;
use RuntimeException;

class ApiProvider
{
    private Client $client;

    /**
     * ApiProvider constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $typeRequest
     * @param string $method
     * @param array $params
     * @param bool $returnHeader
     * @return mixed
     */
    public function callMethod(string $typeRequest, string $method, array $params = [], bool $returnHeader = false)
    {
        usleep(360000);
        $response = $this->client->request($typeRequest, $method, $params);

        if ($response->getStatusCode() != 200) {
            throw new RuntimeException(sprintf(
                "Http status code not 200, got %s status code, message: %s",
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        if ($returnHeader)
        {
            return $response->getHeaders();
        }
        $content = $response->getBody()->getContents();

        /** @var array $response */
        $response = json_decode($content, true);
        return $response;
    }

    public function category(string $store, array $coordinates){
        $self = clone $this;
        return new Category($self, $store, $coordinates);
    }

    public function catalog(string $store){
        $self = clone $this;
        return new Catalog($self, $store);
    }

    public function store(float $latitude, float $longitude){
        $self = clone $this;
        return new Store($self, $latitude, $longitude);
    }


}