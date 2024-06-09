<?php

namespace App\Models;

use App\BinanceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Bot extends Model
{
    use HasFactory;
    protected $table = 'bots'; // Имя таблицы

    protected $fillable = [
        'name', 'exchange_id', 'client_id', 'currency_pairs', 'min_profit_percent', 'min_amount', 'status','exchange_settings_id', 'cancel_settings'
    ];



    /**
     * Настройки модели
     * @return array
     */
    public static function settings()
    {

        return [
            'pl' => 30, // Записей на странице
            'isEdit' => true, // Возможность редактировать
            'isDel' => true, // Возможность удаления
            'isAdd' => true, // Возможность добавления
            'isShow' => false,
            'isCheckbox' => false,
            'isExport' => true,
            'isImport' => true,
            'page' => 'index', // Название страницы
            'title' => "Bots", // Название страницы
            'buttons' => [ // Кнопки для вюхи
                'show'=> __('plans.bshow'),
                'add'=> __('plans.badd'),
                'edit'=> __('plans.bedit'),
                'search'=> __('plans.bsearch'),
                'clear'=> __('plans.bclear'),
                'del'=> __('plans.bdel'),

            ],
            'search_settings' => [  // Настройки для поиска
                'like' => [ // Поля которые используем с неточным совпадением
                    'name',

                ],
                'range' => [ // Поля которые используем с периодом от-до

                ]
            ],
            'table' => self::showInTable(),
            'attr' => self::attr(),
            'form' => self::form_bilder(),
            'searh' => self::form_bilderS()

        ];
    }

    /**
     * Поля и их атрибуты
     * @return array
     */
    public function attr()
    {
        return [
            'name' => "Name",
            'exchange_id' => "Exchange",
            'client_id' => "User",
            'currency_pairs' => "Currency pairs",
            'min_profit_percent' => "Profit percent (min)",
            'min_amount' => "Amount (min)",
            'status' => "Bot status",
            'exchange_settings_id' => 'Account',
            'cancel_settings' => 'Cancel settings'

        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        if (Auth::user()->type=='1') {
            return [
                'name', 'exchange_id','exchange_settings_id', 'currency_pairs', 'min_profit_percent', 'min_amount', 'status'
            ];
        }
        return [
            'name', 'exchange_id','exchange_settings_id', 'client_id', 'currency_pairs', 'min_profit_percent', 'min_amount', 'status'
        ];
    }


    // Для построения формы
    public function form_bilder(){
        if (Auth::user()->type=='1') {
            return [
                'name' => 'text',
                'exchange_id' => [
                    Exchange::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                    [
                        'class' => 'form-control form-select form-select-solid',
                        'prompt' => self::attr()['exchange_id'],
                        'data-kt-select2' => 'true',
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',
                        'required'=>'required'
                    ],
                ],
                'exchange_settings_id' => [
                    ExchangeSetting::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                    [
                        'class' => 'form-control form-select form-select-solid',
                        'prompt' => self::attr()['exchange_settings_id'],
                        'data-kt-select2' => 'true',
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',
                        'required'=>'required'
                    ],
                ],

                'currency_pairs' => [
                    CurrencyPair::selectRaw("CONCAT(buy_currency, ' → ', sell_currency) as name, id")
                        ->where('status', 'TRADING')
                        ->get()
                        ->pluck('name', 'id')
                        ->prepend('-', '')
                        ->toArray(),
                    [
                        'class' => 'form-control form-select form-select-solid listAddS',
                        'prompt' => self::attr()['client_id'],
                        'data-kt-select2' => 'true',
                        //'multiple' => 'multiple',
                        'id' => 'firstSelect',
                        'required'=>'required'
                    ],
                ],
                'cancel_settings' => [
                    self::getCancel_settings(),
                    [
                        'class' => 'form-control form-select form-select-solid',
                        'prompt' => self::attr()['cancel_settings'],
                        'data-kt-select2' => 'true',
                        'multiple' => 'multiple',
                        'required'=>'required'
                    ],
                ],
                'min_profit_percent' => "text",
                'min_amount' => "text",
            ];
        }
        return [
            'name' => 'text',
            'exchange_id' => [
                Exchange::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['exchange_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',
                    'required'=>'required'
                ],
            ],
            'exchange_settings_id' => [
                ExchangeSetting::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['exchange_settings_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',
                    'required'=>'required'
                ],
            ],

            'client_id' => [
                Employee::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['client_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',
                    'required'=>'required'
                ],
            ],
            'currency_pairs' => [
                CurrencyPair::selectRaw("CONCAT(buy_currency, ' → ', sell_currency) as name, id")
                    ->where('status', 'TRADING')
                    ->get()
                    ->pluck('name', 'id')
                    ->prepend('-', '')
                    ->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid listAddS',
                    'prompt' => self::attr()['client_id'],
                    'data-kt-select2' => 'true',
                    //'multiple' => 'multiple',
                    'id' => 'firstSelect',
                    'required'=>'required'
                ],
            ],
            'cancel_settings' => [
                self::getCancel_settings(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['cancel_settings'],
                    'data-kt-select2' => 'true',
                    'multiple' => 'multiple',
                    'required'=>'required'
                ],
            ],
            'min_profit_percent' => "text",
            'min_amount' => "text",
            'status' => [
                self::getStatus(),
                [
                    'class' => 'form-control form-select-solid form-select',
                    'prompt' => self::attr()['status'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                ],
            ],
        ];
    }

    // Для построения формы
    public function form_bilderS(){
        if (Auth::user()->type=='1') {
            return [
                'name' => 'text',
                'exchange_id' => [
                    Exchange::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                    [
                        'class' => 'form-control form-select form-select-solid',
                        'prompt' => self::attr()['exchange_id'],
                        'data-kt-select2' => 'true',
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                    ],
                ],
                'status' => [
                    self::getStatus(),
                    [
                        'class' => 'form-control form-select-solid form-select',
                        'prompt' => self::attr()['status'],
                        'data-kt-select2' => 'true',
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',

                    ],
                ],
            ];
        }
        return [
            'name' => 'text',
            'exchange_id' => [
                Exchange::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['exchange_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                ],
            ],
            'client_id' => [
                Employee::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['client_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                ],
            ],
            'status' => [
                self::getStatus(),
                [
                    'class' => 'form-control form-select-solid form-select',
                    'prompt' => self::attr()['status'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851',

                ],
            ],
        ];
    }

    public function getStatus(){
        return [
            0 => "Work",
            1 => "Stop",
            2 => "Error",
        ];
    }

    public function getCancel_settings(){
        return [
            0 => "Stop if the circle does not match the calculation.",
            1 => "Stop if unable to complete the circle.",
            2 => "Sell remaining balance (in case of errors in the circle).",
        ];
    }

    public function exchangeid()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }

    public function clientid()
    {
        return $this->belongsTo(Employee::class, 'client_id');
    }
    public function exchangesettingsid()
    {
        return $this->belongsTo(ExchangeSetting::class, 'exchange_settings_id');
    }

    public function makeJson()
    {
       // return $this->currency_pairs;
        $data = json_decode($this->currency_pairs);
        //$this->makePairs($data);
        $lastVal = '';
        if ($data !== null) {
            $result = [];
            // Пройдемся по ключам и значениям и добавим их в массив

            foreach ($data as $key => $value) {
                if(trim($lastVal)!=trim(explode(' → ',$value)[0])){
                   // echo $lastVal.'-'.$value;
                $result[] =  $value;
                    $lastVal = explode(' → ',$value)[1];

                }
            }
           // dd($result);
// Объединим элементы массива в строку с разделителем " → "
            $finalString = implode(' → ', $result);
            return $finalString; // Выводим результат
        } else {
            return 'Некорректная JSON-строка';
        }
       // return $this->belongsTo(Employee::class, 'client_id');
    }

    // Распарсим JSON
    public function makePairsv2()
    {
        $data = json_decode($this->currency_pairs);
        //dd($data);
        $result = $data;
        $coinId = 1;
        $orders = '';
        $calcs = '';
        $names = [];
        foreach ($data as $key => $value) {
            $ex = explode(' → ', $value);
            $thisPair = @CurrencyPair::whereRaw("(buy_currency='".$ex[0]."' and sell_currency='".$ex[1]."') OR (buy_currency='".$ex[1]."' and sell_currency='".$ex[0]."')")->first();
            if($thisPair){
                $result[] =  $thisPair;
                $name = $thisPair->sell_currency.$thisPair->buy_currency;
                $varname = "coin".$coinId;
                $lotSizeStepSize = '0.00100000';
                $names[] = "'".$name."'";
                if($coinId==1){
$calcs .= "
    #".$name."
    ".$varname."_convert = convert_request('".$ex[0]."','".$ex[1]."',deposit,1000);
    des = f\"{des}Convert: {".$varname."_convert['toAmount']}/{".$varname."_convert['fromAmount']}, Id:{".$varname."_convert['quoteId']}{os.linesep}\" if 'quoteId' in ".$varname."_convert else f\"{des}Convert: no ID / {".$varname."_convert['toAmount']}/{".$varname."_convert['fromAmount']} {os.linesep}\"
";

$orders .= "
            convert_accept".$coinId." = convert_accept(coin".$coinId."_convert['quoteId'])
            des = f'{des} #1 Convert accept: {convert_accept".$coinId."} | {coin".$coinId."_convert} {os.linesep}'
";

                }else {
$calcs .= "
    #".$name."
    ".$varname."_convert = convert_request('".$ex[0]."','".$ex[1]."',coin".($coinId-1)."_convert['toAmount'],1000);
    des = f\"{des}Convert: {".$varname."_convert['toAmount']}/{".$varname."_convert['fromAmount']}, Id:{".$varname."_convert['quoteId']}{os.linesep}\" if 'quoteId' in ".$varname."_convert else f\"{des}Convert: no ID / {".$varname."_convert['toAmount']}/{".$varname."_convert['fromAmount']} {os.linesep}\"
";
$orders .= "
            coin2_convert = convert_request_quoteId('".$ex[0]."','".$ex[1]."',coin".($coinId-1)."_convert['toAmount'],1000)
            convert_accept".$coinId." = convert_accept(coin".$coinId."_convert['quoteId'])
            des = f'{des} #".$coinId." Convert accept: {convert_accept".$coinId."} | {coin".$coinId."_convert} {os.linesep}'
";
                }
                $coinId++;
            }

        }


        // REVERS
        $coinId = 1;
        $orders_re = '';
        $calcs_re = '';
       // $names = [];
        $data = array_reverse($data);
        foreach ($data as $key => $value) {

            $ex = explode(' → ', $value);
            $thisPair = @CurrencyPair::whereRaw("(buy_currency='".$ex[0]."' and sell_currency='".$ex[1]."') OR (buy_currency='".$ex[1]."' and sell_currency='".$ex[0]."')")->first();
            if($thisPair){
                $ex = array_reverse($ex);
                $result[] =  $thisPair;
                $name = $thisPair->sell_currency.$thisPair->buy_currency;
                $varname = "coin".$coinId;
                $lotSizeStepSize = '0.00100000';

                if($coinId==1){
                    $calcs_re .= "
    #".$name."
    ".$varname."_convert_re = convert_request('".$ex[0]."','".$ex[1]."',deposit,1000);
    des = f\"{des}Convert: {".$varname."_convert_re['toAmount']}/{".$varname."_convert_re['fromAmount']}, Id:{".$varname."_convert_re['quoteId']}{os.linesep}\" if 'quoteId' in ".$varname."_convert_re else f\"{des}Convert: no ID / {".$varname."_convert_re['toAmount']}/{".$varname."_convert_re['fromAmount']} {os.linesep}\"
";

                    $orders_re .= "
            convert_accept".$coinId."_re = convert_accept(coin".$coinId."_convert_re['quoteId'])
            des = f'{des} #1 Convert accept: {convert_accept".$coinId."_re} | {coin".$coinId."_convert_re} {os.linesep}'
";

                }else {
                    $calcs_re .= "
    #".$name."
    ".$varname."_convert_re = convert_request('".$ex[0]."','".$ex[1]."',coin".($coinId-1)."_convert_re['toAmount'],1000);
    des = f\"{des}Convert: {".$varname."_convert_re['toAmount']}/{".$varname."_convert_re['fromAmount']}, Id:{".$varname."_convert_re['quoteId']}{os.linesep}\" if 'quoteId' in ".$varname."_convert_re else f\"{des}Convert: no ID / {".$varname."_convert_re['toAmount']}/{".$varname."_convert_re['fromAmount']} {os.linesep}\"
";
                    $orders_re .= "
            ".$varname."_convert_re = convert_request_quoteId('".$ex[0]."','".$ex[1]."',coin".($coinId-1)."_convert_re['toAmount'],1000)
            convert_accept".$coinId."_re = convert_accept(coin".$coinId."_convert_re['quoteId'])
            des = f'{des} #".$coinId." Convert accept: {convert_accept".$coinId."_re} | {coin".$coinId."_convert_re} {os.linesep}'
";
                }
                $coinId++;
            }

        }
        $implodeNames = implode(",",$names);

        $botID = $this->id;

        $thisAcc = ExchangeSetting::find($this->exchange_settings_id);

        $api_key = @$thisAcc->api_key;
        $secret_key = @$thisAcc->secret_key;
        $step_size = '0.00100000';

        $commission_rate = $thisAcc->purchase_commission;
        $commission_rate_taker = $thisAcc->sale_commission;
        $eventUrl = 'http://181.41.141.146/send-event';
        $deposit = $this->min_amount;
        $minp = $this->min_profit_percent;
        $pricesLen = sizeof($data);

        $pythonCode = <<<python
import json
import time
import requests
from binance.client import Client
from binance.exceptions import BinanceAPIException
import asyncio
import math
import os
from datetime import datetime


# Ваши API-ключ и секретный ключ
api_key = '$api_key'
api_secret = '$secret_key'
client = Client(api_key, api_secret)


def calculate_order_quantity(coins_available, step_size):
    if coins_available > 1:
        step_size = '$step_size'
    ticks = step_size.find('1') - 1
    return math.floor(coins_available * 10**ticks) / 10**ticks

def writetofile(msg):
    if msg:
        file_path = "/var/www/html/storage/app/bots/data$botID.txt"
        try:
            with open(file_path, 'a') as file:
                file.write(str(msg) + os.linesep)
        except FileNotFoundError:
            print(f"FileNotFoundError: {file_path}")

def convert_request(fromAsset,toAsset,fromAmount,toAmount):
    params = {
        "fromAsset": fromAsset,
        "toAsset": toAsset,
        "fromAmount": fromAmount,
        "toAmount": toAmount
    }
    return client.convert_request_quote(**params)

def convert_request_quoteId(fromAsset,toAsset,fromAmount,toAmount):
    params = {
        "fromAsset": fromAsset,
        "toAsset": toAsset,
        "fromAmount": fromAmount,
        "toAmount": toAmount
    }
    while True:
        rez = client.convert_request_quote(**params)
        if 'quoteId' in rez:
            return rez
        time.sleep(0.5)



def convert_accept(quote_id):
    params = {
        "quoteId": quote_id,
        "recvWindow": 5000
    }
    while True:
        rez = client.convert_accept_quote(**params)
        if rez['orderStatus'] == 'SUCCESS':
            return rez
        time.sleep(0.5)

async def get_prices(symbols):
    prices = {}
    tickers = client.get_all_tickers()

    for ticker in tickers:
        symbol = ticker['symbol']
        if symbol in symbols:
            price = float(ticker['lastPrice'])
            print(f"{symbol}: {price}")
            prices[symbol] = price

    return prices

def get_prices(symbols):
    prices = {}

    for symbol in symbols:
        ticker = client.get_ticker(symbol=symbol)
        price = float(ticker['lastPrice'])
        print(f"{symbol}: {price}")
        prices[symbol] = price

    return prices


def calculate_and_print(symbols_to_check):
    step_size = '$step_size'
    commission_rate = $commission_rate
    commission_rate_taker = $commission_rate_taker
    iteration_count = 0  
    minp  = $minp
    deposit = $deposit
    des = f"Classic trade:{os.linesep}"

    prices = get_prices(symbols_to_check)


    $calcs
    result = float(coin3_convert['toAmount'])
    percentage_difference = ((result / deposit) * 100) - 100
    des = f"{des} result: {result}({percentage_difference}%){os.linesep}"


    des = f"{os.linesep} {des} "

    des = f"{des} {os.linesep}{os.linesep} Revers trade:{os.linesep}"

    #----------------------------
    $calcs_re 
    
    result2 = float(coin3_convert_re['toAmount'])

    percentage_difference2 = ((result2 / deposit) * 100) - 100
    des = f"{des} result: {result2}({percentage_difference2}%){os.linesep}"




    # Используйте корректные ключевые слова True и False
    if percentage_difference >= minp or percentage_difference2 >= minp:
        des = f"{des} Percentage difference > minimum Percentage {os.linesep}"
        if result2 > result:
            des = f"{des} Revers > Classic, try convert... {os.linesep}"
            $orders_re
        else:
            des = f"{des} Revers < Classic, try convert... {os.linesep}"
            $orders

        data = {
            #'prices': prices,
            'result': result,
            'des': des
        }
        return data
    else:
        data = {
            #'prices': prices,
            'result': result,
            'des': des
        }
        return data


symbols = [$implodeNames]


python;

$pythonOne = <<<python
rez = calculate_and_print(symbols)
print(rez['des'])

python;


        // Укажите путь к файлу Python
        $pythonFilePath = '/var/www/html/storage/app/bots/testv2bot'.$botID.'.py';

// Запись кода Python в файл
        file_put_contents($pythonFilePath, $pythonCode.$pythonOne);


$pythonCode .= <<<python
while True:
    rez = calculate_and_print(symbols)
    writetofile(rez['des'])
    print(rez['des'])
python;
        // Укажите путь к файлу Python
        $pythonFilePath = '/var/www/html/storage/app/bots/v2bot'.$botID.'.py';

// Запись кода Python в файл
        file_put_contents($pythonFilePath, $pythonCode);

        return $pythonCode;
    }
    // Распарсим JSON
    public function makePairs()
    {
        $data = json_decode($this->currency_pairs);
        //dd($data);
        $result = $data;
        $coinId = 1;
        $orders = '';
        $calcs = '';
        $names = [];
        $orders.= "
                bnbsum = 0               
                ";
        foreach ($data as $key => $value) {
            $ex = explode(' → ', $value);
            $thisPair = @CurrencyPair::whereRaw("(buy_currency='".$ex[0]."' and sell_currency='".$ex[1]."') OR (buy_currency='".$ex[1]."' and sell_currency='".$ex[0]."')")->first();
            if($thisPair){
            $result[] =  $thisPair;
            $name = $thisPair->sell_currency.$thisPair->buy_currency;
            $varname = "coin".$coinId;
                $dataPair = json_decode($thisPair->orderTypes, true);
                $lotSizeStepSize = null;

                foreach ($dataPair as $filter) {
                    if ($filter['filterType'] === 'MARKET_LOT_SIZE') {
                        // Extract stepSize from LOT_SIZE
                        $lotSizeStepSize = $filter['stepSize'];
                       // dd($lotSizeStepSize);
                        break; // Stop the loop since you found what you needed
                    }
                }
                $lotSizeStepSize = '0.000100000';


            $names[] = "'".$name."'";
            if($ex[0]==$thisPair->sell_currency){

                if($coinId==1){
                $orders.= "des = f'{des} Firs target: {[coin".($coinId)."_amount_re,coin".($coinId)."_amount]} {os.linesep}'
                order_results['".$value."'] = execute_order('".$name."', 'SELL', ".$varname."_amount, ".$varname."_amount_re)
              
                
                if not order_results['".$value."']:
                    des = f\"Stop! Error make order: {order_results['".$value."']}\"
                    writetofile(des)
                    print(des)
                    return False
                executed_quantity = float(order_results['".$value."'].get('executedQty', 0))
                thisrez = order_results['".$value."']
                orders_str = f'Make order: ".$name."/SELLL={executed_quantity} {thisrez}'
                des = f'{des}{os.linesep}{orders_str}{os.linesep}'   
                          
                ";
                    if(sizeof($data)>($coinId)){
                        $orders.= "
                 
                result_calculate = calculate_rez(order_results['".$value."']) 
                if result_calculate['bnb'] == 1:
                    thiscommission = result_calculate['commission_sum']
                    bnbsum += float(thiscommission)
                    thisprice = result_calculate['average_price']
                    thisqty = result_calculate['qty_sum']
                    des = f'{des}Commission in BNB: {thiscommission} | Avg price: {thisprice} | Next target: {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    next_rez = find_closest_number(thisqty, [coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount])
                    if coin".($coinId+1)."_amount_re == next_rez:
                        coin".($coinId+1)."_amount_re = difference_function(thisqty, coin".($coinId+1)."_amount_re)
                    else:
                        coin".($coinId+1)."_amount = difference_function(thisqty, coin".($coinId+1)."_amount)
                    des = f'{des} This order rezult: {thisqty} | Next target (fix): {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    
                else:
                    thiscommission = result_calculate['commission_sum']
                    des = f'{des}Commission not BNB: {thiscommission}  {os.linesep}'                
                                    
                ";
                    }
                }else {
                    $orders.= "order_results['".$value."'] = execute_order('".$name."', 'SELL', ".$varname."_amount_re, ".$varname."_amount)
               
                if not order_results['".$value."']:
                    des = f\"Stop! Error make order: {order_results['".$value."']}\"
                    writetofile(des)
                    print(des)                
                    return False
                executed_quantity = float(order_results['".$value."'].get('executedQty', 0))
                thisrez = order_results['".$value."']
                orders_str = f'Make order: ".$name."/SELL={executed_quantity} {thisrez}'
                des = f'{des}{os.linesep}{orders_str}{os.linesep}'
            
                                    
                ";
if(sizeof($data)>($coinId)){
    $orders.= "
                 
                result_calculate = calculate_rez(order_results['".$value."']) 
                if result_calculate['bnb'] == 1:
                    thiscommission = result_calculate['commission_sum']
                    bnbsum += float(thiscommission)
                    thisprice = result_calculate['average_price']
                    thisqty = result_calculate['qty_sum']
                    des = f'{des}Commission in BNB: {thiscommission} | Avg price: {thisprice} | Next target: {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    next_rez = find_closest_number(thisqty, [coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount])
                    if coin".($coinId+1)."_amount_re == next_rez:
                        coin".($coinId+1)."_amount_re = difference_function(thisqty, coin".($coinId+1)."_amount_re)
                    else:
                        coin".($coinId+1)."_amount = difference_function(thisqty, coin".($coinId+1)."_amount)
                    des = f'{des} This order rezult: {thisqty} | Next target (fix): {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    
                else:
                    thiscommission = result_calculate['commission_sum']
                    des = f'{des}Commission not BNB: {thiscommission}  {os.linesep}'                
                                    
                ";
}
                }


            }else {
                if($coinId==1){
                $orders.= "
                des = f'{des} Firs target: {[coin".($coinId)."_amount_re,coin".($coinId)."_amount]} {os.linesep}'
                order_results['".$value."'] = execute_order('".$name."', 'BUY', ".$varname."_amount, ".$varname."_amount_re)
                if not order_results['".$value."']:
                    des = f\"Stop! Error make order: {order_results['".$value."']}\"
                    writetofile(des)
                    print(des)                
                    return False         
                    
                executed_quantity = float(order_results['".$value."'].get('executedQty', 0))
                thisrez = order_results['".$value."']
                orders_str = f'Make order: ".$name."/BUY={executed_quantity} {thisrez}'   
                des = f'{des}{os.linesep}{orders_str}{os.linesep}'   
                first_spend = find_closest_number(deposit, [float(order_results['".$value."']['executedQty']),float(order_results['".$value."']['cummulativeQuoteQty'])])
                des = f'{des} Deposit/Real expenses = {deposit}/{first_spend} {os.linesep}'  
                ";

                }else {


                    $orders.= "
                
                order_results['".$value."'] = execute_order('".$name."', 'BUY', ".$varname."_amount, ".$varname."_amount_re)
                if not order_results['".$value."']:
                    des = f\"Stop! Error make order: {order_results['".$value."']}\"
                    writetofile(des)
                    print(des)                
                    return False         
                    
                executed_quantity = float(order_results['".$value."'].get('executedQty', 0))
                thisrez = order_results['".$value."']
                orders_str = f'Make order: ".$name."/BUY={executed_quantity} {thisrez}'   
                des = f'{des}{os.linesep}{orders_str}{os.linesep}'   
                  
                ";
                }
if(sizeof($data)>($coinId)){
                    $orders.= "
                 
                result_calculate = calculate_rez(order_results['".$value."']) 
                if result_calculate['bnb'] == 1:
                    thiscommission = result_calculate['commission_sum']
                    bnbsum += float(thiscommission)
                    thisprice = result_calculate['average_price']
                    thisqty = result_calculate['qty_sum']
                    des = f'{des}Commission in BNB: {thiscommission} | Avg price: {thisprice} | Next target: {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    next_rez = find_closest_number(thisqty, [coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount])
                    if coin".($coinId+1)."_amount_re == next_rez:
                        coin".($coinId+1)."_amount_re = thisqty
                    else:
                        coin".($coinId+1)."_amount = thisqty
                    des = f'{des} This order rezult: {thisqty} | Next target (fix): {[coin".($coinId+1)."_amount_re,coin".($coinId+1)."_amount]} {os.linesep}'
                    
                else:
                    thiscommission = result_calculate['commission_sum']
                    des = f'{des}Commission not BNB: {thiscommission}  {os.linesep}'                
                                    
                ";
                }

            }
                if($coinId==sizeof($data)) {
                    $orders .= "
                last_get = find_closest_number(first_spend, [float(order_results['" . $value . "']['executedQty']),float(order_results['" . $value . "']['cummulativeQuoteQty'])])
                percentage_difference_real = ((last_get / first_spend) * 100) - 100
                formatted_commission2 = '{:.8f}'.format(bnbsum)
                des = f'{des}{os.linesep} Deposit/Real get = {first_spend}/{last_get} {os.linesep} Real result: {percentage_difference_real}%{os.linesep} Commission in BNB: {formatted_commission2}{os.linesep}'  
                
                ";
                }
            if($coinId==1){
$calcs.= "
                
                ".$varname."_amount = deposit / prices['".$name."']
                
                ".$varname."_amount = float(round(".$varname."_amount, 8))
                ".$varname."_amount_tmp = ".$varname."_amount
                rez_order1 = calculate_order_quantity((deposit / prices['".$name."']), '".$lotSizeStepSize."')
                
                #if rez_order1<=0: rez_order1 = ".$varname."_amount 
                #if rez_order1<=0: rez_order1 = ".$varname."_amount 
                
                        
                ".$varname."_amount = rez_order1
                ".$varname."_amount = calculate_order_quantity(".$varname."_amount, '".$lotSizeStepSize."')
                ".$varname."_amount_re = calculate_order_quantity(deposit, '".$lotSizeStepSize."')
                
                
                deposit = rez_order1 * prices['".$name."']
                deposit = float(round(deposit, 8))
                
                ".$varname."_purchase_commission = ".$varname."_amount * commission_rate
                deposit = deposit + ".$varname."_purchase_commission
                result = deposit - ".$varname."_purchase_commission
                des = f'{des}{os.linesep} #{$coinId}-(".$name.") calc: {".$varname."_amount}/{".$varname."_amount_re}'
        
";
            }else {
                if($ex[0]==$thisPair->sell_currency){
$calcs .= "
                
                ".$varname."_amount = prices['".$name."'] * coin".($coinId-1)."_amount
                ".$varname."_amount_re = calculate_order_quantity((coin".($coinId-1)."_amount - (coin".($coinId-1)."_amount * commission_rate)), '".$lotSizeStepSize."')
                
                ".$varname."_amount = round(".$varname."_amount, 8)
                ".$varname."_amount_tmp = ".$varname."_amount
                ".$varname."_amount = calculate_order_quantity(".$varname."_amount, '".$lotSizeStepSize."')
                ".$varname."_purchase_commission = ".$varname."_amount * commission_rate_taker
                des = f'{des}{os.linesep} #{$coinId}-(".$name.") calc: {".$varname."_amount}/{".$varname."_amount_re}'
                

";
                }else {
$calcs .= "
                ".$varname."_amount = coin".($coinId-1)."_amount / prices['".$name."']
                ".$varname."_amount_re = calculate_order_quantity((coin".($coinId-1)."_amount - (coin".($coinId-1)."_amount * commission_rate)), '".$lotSizeStepSize."')
                
                ".$varname."_amount = round(".$varname."_amount, 8)
                ".$varname."_amount_tmp = ".$varname."_amount
                ".$varname."_amount = calculate_order_quantity(".$varname."_amount, '".$lotSizeStepSize."')
                ".$varname."_purchase_commission = ".$varname."_amount * commission_rate
                des = f'{des}{os.linesep}#{$coinId}-(".$name.") calc: {".$varname."_amount}/{".$varname."_amount_re}'

";
                }

if($coinId==sizeof($data)){
    $calcs .= "
                result = ".$varname."_amount - ".$varname."_purchase_commission
";
}
            }

            $coinId++;
            }
        }
$implodeNames = implode(",",$names);



        $botID = $this->id;

        $thisAcc = ExchangeSetting::find($this->exchange_settings_id);

        $getBal = new BinanceService($thisAcc->api_key,$thisAcc->secret_key);
        $rez = $getBal->getAccountBalances();
       // dd($rez);
        $commissionRates = $rez['commissionRates'];
        $thisAcc->purchase_commission = $commissionRates['maker'];
        $thisAcc->sale_commission = $commissionRates['taker'];
        $thisAcc->save();


        $api_key = @$thisAcc->api_key;
        $secret_key = @$thisAcc->secret_key;
        $step_size = '0.00010000';

        $commission_rate = $thisAcc->purchase_commission;
        $commission_rate_taker = $thisAcc->sale_commission;
        $eventUrl = 'http://181.41.141.146/send-event';
        $deposit = $this->min_amount;
        $minp = $this->min_profit_percent;
        $pricesLen = sizeof($data);


        $pythonCode = <<<python
import json
import time
import requests
from binance.client import Client
from binance.exceptions import BinanceAPIException
import asyncio
import math
import os
from datetime import datetime

# Ваши API-ключ и секретный ключ
api_key = '$api_key'
api_secret = '$secret_key'
client = Client(api_key, api_secret)

def calculate_rez(thisrez):
    qty_sum = 0
    price_sum = 0
    commission_sum = 0
    commission_asset_condition = 'BNB'
    
    for fill in thisrez['fills']:
        qty_sum += float(fill['qty'])
        price_sum += float(fill['price'])
        commission_sum += float(fill['commission'])
    
    average_price = price_sum / len(thisrez['fills'])
    
    formatted_commission = '{:.8f}'.format(commission_sum)

    data = {
            'qty_sum': qty_sum,
            'average_price': average_price,
            'commission_sum': formatted_commission,
            'bnb': 1
        }
    return data

def difference_function(thisqty, coin):
    allowable_difference = 0.1 * coin
    
    if coin - allowable_difference <= thisqty <= coin + allowable_difference:
        return thisqty
    else:
        return coin
                
def find_closest_number(target_number, numbers):
    closest_number = min(numbers, key=lambda x: abs(x - target_number))
    return closest_number
        
def calculate_order_quantity(coins_available, step_size):
    if coins_available > 1:
        step_size = '0.01000000'
        
    ticks = step_size.find('1') - 1
    order_quantity = math.floor(coins_available * 10**ticks) / 10**ticks
    return order_quantity

def writetofile(msg):
    if msg:
        file_path = "/var/www/html/storage/app/bots/data$botID.txt"
        try:
            with open(file_path, 'a') as file:
                file.write(str(msg) + os.linesep)  # Преобразуем объект исключения в строку перед добавлением
    
            with open(file_path, 'r') as file:
                lines = file.readlines()
    
            if len(lines) > 10000:
                with open(file_path, 'w') as file:
                    file.writelines(lines[:10000])
                    #print("Log clear...")
                #print("File write...")
    
        except FileNotFoundError:
            print(f"File not found: {file_path}")

def place_market_order(symbol, side, quantity):
    try:
        order = client.create_order(
            symbol=symbol,
            side=side,
            type='MARKET',
            quantity=quantity
        )
        return order
        
    except BinanceAPIException as e:
        msg = f"Error creating an order ({symbol}/{side}/{quantity}): {e}"
        print(msg)
        writetofile(msg)
        
        return False
    except Exception as e:
        print(f"Произошла неожиданная ошибка: {e}")
        return False
            
def place_market_order_re(symbol, side, quantity):
    try:
        order = client.create_order(
            symbol=symbol,
            side=side,
            type='MARKET',
            quoteOrderQty=quantity
        )
        return order    
        
    except BinanceAPIException as e:
        msg = f"Error creating an order ({symbol}/{side}/{quantity}): {e}"
        print(msg)
        writetofile(msg)
        
        return False

    except Exception as e:
        print(f"Произошла неожиданная ошибка: {e}")
      
        return False
         

     
def execute_order(symbol, side, amount, amount_re):
    if amount > amount_re:
        msg = f"symbol:{symbol} | side:{side} | amount:{amount} "
        print(msg)
        writetofile(msg)
        rez_execute_order = place_market_order(symbol, side, amount)
        
        if not rez_execute_order:
            rez_execute_order = place_market_order(symbol, side, round(amount))
            print("Try#2")
            
    else:
        msg = f"symbol:{symbol} | side:{side} | amount:{amount_re} "
        print(msg)
        writetofile(msg)
        rez_execute_order = place_market_order_re(symbol, side, amount_re)
        
        if not rez_execute_order:
            rez_execute_order = place_market_order_re(symbol, side, round(amount_re))
            print("Try#2")
                    
    
    return rez_execute_order

def get_price(symbol):
    prices = {}
    for symbol in symbols:
        ticker = client.get_ticker(symbol=symbol)
        price = float(ticker['lastPrice'])
        print(f"{symbol}: {price}")
        prices[symbol] = price
    return prices
    
def subscribe_to_symbols(symbols, prices):
    tasks = [get_price(symbol, prices) for symbol in symbols]
    #await asyncio.gather(*tasks)

def process_prices(prices,data_list):
        if len(prices) == $pricesLen:
            deposit = $deposit
            minp = $minp
            des = '';
            iteration_count = 0  # Инициализация счетчика циклов
            step_size = '$step_size'
            commission_rate = $commission_rate
            commission_rate_taker = $commission_rate_taker
            if iteration_count == 0:             
                
                $calcs                
                percentage_difference = ((result / deposit) * 100) - 100
        
        

        
                des = f"{des}{os.linesep}{os.linesep} Result is {percentage_difference:.2f}% {'higher' if deposit < result else 'lower'} than Deposit. {'Buy' if percentage_difference >= minp else ''}{os.linesep}{os.linesep}"
       
                    
           
            orders_str = ' .... '
            writetofile(des)
            print(des)
                        
            if percentage_difference >= minp:                
                des = f"{os.linesep} Start orders..."
                #print(des)
                order_results = {}
                $orders
                
                
                writetofile(des)
                print(des)
                
                minusCom = 0.0003
                minusCom = round(minusCom, 8)


            if percentage_difference >= (minp):
                url = "$eventUrl"
                data = {
                    'prices': prices,
                    'result': result,
                    'deposit': deposit,
                    'bot_id': $botID,
                    'order_results': order_results
                }
                response = requests.post(url, json=data)

                if response.status_code == 200:
                    print("send Event")
                else:
                    print(f"{response.text}Err:", response.status_code, response.text)

            prices = {}

        return False


if __name__ == "__main__":
    symbols = [$implodeNames]
    prices = {}
    data_list = []
    

python;


$pythonOne = <<<python
    while True:
        #process_prices(prices,data_list)
        prices = get_price(symbols)        
        result = process_prices(prices, data_list)
        print('prep')
        time.sleep(1)
        #if not result:
           # break  # Останавливаем цикл
python;


// Укажите путь к файлу Python
        $pythonFilePath = '/var/www/html/storage/app/bots/bot'.$botID.'.py';


// Запись кода Python в файл
        file_put_contents($pythonFilePath, $pythonCode.$pythonOne);


        $pythonCode .= <<<python
    print('Start')
    current_datetime = datetime.now()        
    formatted_datetime = current_datetime.strftime("%Y-%m-%d %H:%M:%S") 
    print(f"{formatted_datetime}{os.linesep}")   
    prices = get_price(symbols)        
    result = process_prices(prices, data_list)
    print('End')        
        
python;
        // Укажите путь к файлу Python
        $pythonFilePath = '/var/www/html/storage/app/bots/testbot'.$botID.'.py';

// Запись кода Python в файл
        file_put_contents($pythonFilePath, $pythonCode);
    return $pythonCode;
//        print_r($calcs);
//        print_r($orders);
//        dd($pythonCode);

    }

    public function calculateDifferenceSum()
    {
        // Используем selectRaw для создания выражений суммирования в рамках одного запроса
        $result = TradeHistory::where('bot_id', $this->id)
            ->selectRaw('SUM(predicted_profit_percent) - SUM(amount) as difference_sum, SUM(predicted_profit_percent) as psum, SUM(amount) as asum')
            ->first();

        // Извлекаем результат
        $difference = $result ?? 0;

        return $difference;
    }
}
