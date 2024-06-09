<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class TradeHistory extends Model
{
    use HasFactory;
    protected $table = 'trade_history'; // Имя таблицы

    protected $fillable = [
        'bot_id', 'exchange_id', 'client_id', 'currency_pairs', 'predicted_profit_percent', 'amount', 'status', 'order_id', 'prices'
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
            'isDel' => false, // Возможность удаления
            'isAdd' => false, // Возможность добавления
            'isShow' => false,
            'isCheckbox' => false,
            'isExport' => true,
            'isImport' => true,
            'page' => 'index', // Название страницы
            'title' => "Trade History", // Название страницы
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

    public function getStatus(){
        return [
            0 => "Work",
            1 => "Done",
            2 => "Error",
        ];
    }
    /**
     * Поля и их атрибуты
     * @return array
     */
    public function attr()
    {
        return [
            'exchange_id' => "Exchange",
            'bot_id' => "Bot",
            'client_id' => "Client",
            'currency_pairs' => "Currency pairs",
            'predicted_profit_percent' => "Predicted profit ",
            'amount' => "Amount",
            'status' => "Status",
            'order_id' => "Order id",
            'prices' => "Details",
            'created_at' => 'Date&time'

        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        if (Auth::user()->type=='1') {
            return [
                'bot_id', 'exchange_id', 'currency_pairs', 'predicted_profit_percent', 'amount', 'status', 'prices','created_at'
            ];
        }
        return [
            'bot_id', 'exchange_id', 'client_id', 'currency_pairs', 'predicted_profit_percent', 'amount', 'status', 'prices','created_at'
        ];
    }


    // Для построения формы
    public function form_bilder(){
        if (Auth::user()->type=='1') {
            return [
                'exchange_id' => 'text',
                'bot_id' => "text",
                'currency_pairs' => "text",
                'predicted_profit_percent' => "text",
                'amount' => "text",
                'status' => "text",
                'order_id' => "text",
                'prices' => "text",
            ];
        }
        return [
            'exchange_id' => 'text',
            'bot_id' => "text",
            'client_id' => "text",
            'currency_pairs' => "text",
            'predicted_profit_percent' => "text",
            'amount' => "text",
            'status' => "text",
            'order_id' => "text",
            'prices' => "text",
        ];
    }

    // Для построения формы
    public function form_bilderS(){
        return [
            'exchange_id' => 'text',
            'bot_id' => "text",

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
    public function botid()
    {
        return $this->belongsTo(Bot::class, 'bot_id');
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
    function formtPrices() {
        $jsonString = $this->prices;
        $data = json_decode($jsonString, true);

        if (!$data) {
            return "Invalid JSON format.";
        }

        $formattedData = "";

        foreach ($data as $pair => $trade) {
            $formattedData .= "$pair:\n";
            $formattedData .= "  Side: {$trade['side']}\n";
            $formattedData .= "  Type: {$trade['type']}\n";
            $formattedData .= "  Status: {$trade['status']}\n";
            $formattedData .= "  Executed Quantity: {$trade['executedQty']}\n";
            $formattedData .= "  Cummulative Quote Quantity: {$trade['cummulativeQuoteQty']}\n";
            $formattedData .= "  Fills:\n";

            foreach ($trade['fills'] as $fill) {
                $formattedData .= "    Qty: {$fill['qty']}, Price: {$fill['price']}, Trade ID: {$fill['tradeId']}\n";
            }

            $formattedData .= "\n";
        }

        return $formattedData;
    }

}
