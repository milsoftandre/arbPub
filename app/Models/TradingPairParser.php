<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;


class TradingPairParser extends Model
{
    use HasFactory;
    public function parseAndStoreTradingPairs()
    {
        $url = "https://api.binance.com/api/v3/exchangeInfo";
        $response = Http::get($url);

        if ($response->ok()) {
            $data = $response->json();
            $pairs = collect($data['symbols']);
//dd($pairs[1]['filters']);
            foreach ($pairs as $pair) {
                // Используйте данные из $pair для новых полей
                $data = [
                    'exchange_id' => 1,
                    'sell_currency' => $pair['baseAsset'],
                    'buy_currency' => $pair['quoteAsset'],
                    'status' => $pair['status'],
                    'baseAssetPrecision' => $pair['baseAssetPrecision'],
                    'quotePrecision' => $pair['quotePrecision'],
                    'quoteAssetPrecision' => $pair['quoteAssetPrecision'],
                    'baseCommissionPrecision' => $pair['baseCommissionPrecision'],
                    'quoteCommissionPrecision' => $pair['quoteCommissionPrecision'],
                    'orderTypes' => json_encode($pairs[1]['filters']),
                ];

                // Поиск записи по заданным условиям
                $currencyPair = CurrencyPair::updateOrCreate(
                    [
                        'exchange_id' => 1,
                        'sell_currency' => $pair['baseAsset'],
                        'buy_currency' => $pair['quoteAsset']
                    ],
                    $data
                );
            }

            return "Торговые пары успешно спарсены и обработаны.";
        }

        return "Ошибка при выполнении запроса к Binance API.";
    }

}
