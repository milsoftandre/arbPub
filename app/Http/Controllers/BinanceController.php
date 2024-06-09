<?php

namespace App\Http\Controllers;

use App\BinanceService;
use App\Models\Bot;
use App\Models\CurrencyPair;
use Illuminate\Http\Request;
class BinanceController extends Controller
{

    public function getBalances()
    {

        $balances = $this->binanceService->getAccountBalances();

        // Обработка ответа и вывод балансов
        dd($balances);
    }

    public function makeOrder(Request $request)
    {
        $thisAcc = \App\Models\ExchangeSetting::find(1);

        $binanceService = new \App\BinanceService($thisAcc->api_key,$thisAcc->secret_key);

        // Получение данных из формы
        $currencyPairId = $request->input('CurrencyPair');
        $quantity = $request->input('quantity');
        $orderType = $request->input('ordertype');
        $price = $request->input('price');
        $type = $request->input('type');

        // Получение данных о паре валют из модели CurrencyPair
        $currencyPair = \App\Models\CurrencyPair::findOrFail($currencyPairId);

        // Формирование символа (например, BTCUSDT)
        $symbol = $currencyPair->sell_currency . $currencyPair->buy_currency;

        // Вызов методов BinanceService для создания ордера
        $result = $binanceService->placeOrder($symbol, $type, $quantity, $orderType, $price);
//dd($result);
        // Возвращаем результаты ордера
        return response()->json($result);
    }

    public function getPrice(Request $request)
    {
        $id = $request->input('id');
        $bot = Bot::find($id);
        $thisAcc = \App\Models\ExchangeSetting::find(1);

        $binanceService = new \App\BinanceService($thisAcc->api_key,$thisAcc->secret_key);
        $data = json_decode($bot->currency_pairs);
        $result = $data;
        $coinId = 1;
        $orders = '';
        $calcs = '';
        $names = [];
        $deposit = 10;
        $commission = 0.001;
        $coin = [];
        //dd($data);
        foreach ($data as $key => $value) {
           // echo $coinId;
            $ex = explode(' → ', $value);
            $thisPair = @CurrencyPair::whereRaw("(buy_currency='" . $ex[0] . "' and sell_currency='" . $ex[1] . "') OR (buy_currency='" . $ex[1] . "' and sell_currency='" . $ex[0] . "')")->first();
            if ($thisPair) {
                $result[] = $thisPair;
                $symbol = $thisPair->sell_currency . $thisPair->buy_currency;
// Получение цены для пары BTCUSDT
                //$symbol = 'BTCUSDT';
                $priceData = $binanceService->getSymbolPrice($symbol)['price'];
//dd($priceData);
                $coinp[] = $symbol;
                if($coinId==1){
                    $coin[$coinId] = $deposit / $priceData;
                    $calcs.= "<br><br>
       
        ".$symbol." = ".$deposit." / ".$priceData."<br>
        result = ".$deposit." - ".$commission."<br>
        
";
                }else {
                   // dd(($coinId-1));
                    $coin[$coinId] = $priceData * $coin[($coinId-1)];
                    if($ex[0]==$thisPair->sell_currency){
                        $calcs .= "<br><br>
        ".$symbol." = ".$priceData." * ".$coin[($coinId-1)]."<br>
        commission = ".$priceData." * ".$commission."<br>

";
                    }else {
                        $coin[$coinId] = $coin[($coinId-1)] /  $priceData;
                        $calcs .= "<br><br>
        ".$symbol." = ".$coin[($coinId-1)]." / ".$priceData."<br>
        commission = ".$priceData." * ".$commission."<br>

";
                    }

                    if($coinId==sizeof($data)){
                        $calcs .= "<br>
        result = ".$priceData." - ".($priceData * $commission)."
";
                    }
                }


// Вывод цены в консоль

            }
            $coinId++;
        }
        echo $calcs;
    }
}
