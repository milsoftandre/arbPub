<?php

namespace App;

use GuzzleHttp\Client;

class BinanceService
{
    protected $apiUrl;
    protected $apiKey;
    protected $apiSecret;

    public function __construct($apiKey,$apiSecret)
    {
        $this->apiUrl = 'https://api.binance.com/api/v3';
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function getAccountBalances()
    {
        $timestamp = now()->timestamp * 1000; // текущее время в миллисекундах
        $recvWindow = 5000; // 5 seconds

        $query = "recvWindow=$recvWindow&timestamp=$timestamp";
        $signature = hash_hmac('sha256', $query, $this->apiSecret);

        $client = new Client();

        $response = $client->request('GET', $this->apiUrl . '/account', [
            'query' => [
                'recvWindow' => $recvWindow,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ],
            'headers' => [
                'X-MBX-APIKEY' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }


    public function getOrderById($orderId)
    {
        $timestamp = now()->timestamp * 1000; // текущее время в миллисекундах
        $recvWindow = 5000; // 5 seconds

        // Используйте "ALL" для символа, чтобы запросить информацию об ордерах по всем символам
        $query = "symbol=XMRBTC&orderId=$orderId&recvWindow=$recvWindow&timestamp=$timestamp";
        $signature = hash_hmac('sha256', $query, $this->apiSecret);

        $client = new Client();

        $response = $client->request('GET', $this->apiUrl . '/order', [
            'query' => [
                'symbol' => 'XMRBTC',
                'orderId' => $orderId,
                'recvWindow' => $recvWindow,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ],
            'headers' => [
                'X-MBX-APIKEY' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }


    public function placeOrder($symbol, $side, $quantity, $type = 'MARKET', $price = null)
    {
        $timestamp = now()->timestamp * 1000; // current time in milliseconds
        $recvWindow = 5000; // 5 seconds

        $params = [
            'symbol' => $symbol,
            'side' => strtoupper($side),
            'type' => strtoupper($type),
           // 'quantity' => $quantity,
            'quoteOrderQty' => $quantity,
            'timestamp' => $timestamp,
            'recvWindow' => $recvWindow,
        ];

        // If the order type is LIMIT, add the price to the parameters
        if ($type === 'LIMIT' && !is_null($price)) {
            $params['price'] = $price;
        }

        // Sort the parameters alphabetically
        ksort($params);

        // Create the query string
        $query = http_build_query($params, '', '&');

        // Create the signature using HMAC-SHA256
        $signature = hash_hmac('sha256', $query, $this->apiSecret);

        $params['signature'] = $signature;

        $client = new Client();

        try {
            $response = $client->request('POST', $this->apiUrl . '/order', [
                'form_params' => $params,
                'headers' => [
                    'X-MBX-APIKEY' => $this->apiKey,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle bad request (400) and return the error response
            $response = $e->getResponse();
            return json_decode($response->getBody(), true);
        }
    }


    public function buy($symbol, $quantity, $type = 'MARKET', $price = null)
    {
        return $this->placeOrder($symbol, 'BUY', $quantity, $type, $price);
    }

    public function sell($symbol, $quantity, $type = 'MARKET', $price = null)
    {
        return $this->placeOrder($symbol, 'SELL', $quantity, $type, $price);
    }

    public function getSymbolPrice($symbol)
    {
        $client = new Client();

        $response = $client->request('GET', $this->apiUrl . '/ticker/price', [
            'query' => [
                'symbol' => $symbol,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
