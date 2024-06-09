<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyPair extends Model
{
    use HasFactory;
    protected $table = 'currency_pairs'; // Имя таблицы

    protected $fillable = [
        'exchange_id', 'sell_currency', 'buy_currency', 'status', 'baseAssetPrecision', 'quotePrecision', 'quoteAssetPrecision', 'baseCommissionPrecision', 'quoteCommissionPrecision', 'orderTypes',
    ];



    /**
     * Настройки модели
     * @return array
     */
    public static function settings()
    {

        return [
            'pl' => 30, // Записей на странице
            'isEdit' => false, // Возможность редактировать
            'isDel' => false, // Возможность удаления
            'isAdd' => false, // Возможность добавления
            'isShow' => false,
            'isCheckbox' => false,
            'isExport' => true,
            'isImport' => true,
            'page' => 'index', // Название страницы
            'title' => "Exchanges Currency Pair", // Название страницы
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
            'exchange_id' => "Exchange",
            'sell_currency' => "Sell currency",
            'buy_currency' => "Buy currency",
            'status' => "Status",
            'baseAssetPrecision' => "Base Asset Precision",
            'quotePrecision' => "Quote Precision",
            'quoteAssetPrecision' => "Quote Asset Precision",
            'baseCommissionPrecision' => "Base Commission Precision",
            'quoteCommissionPrecision' => "Quote Commission Precision",
            'orderTypes' => "Order Types",
        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        return [
            'exchange_id', 'sell_currency', 'buy_currency', 'status', 'baseAssetPrecision', 'quotePrecision', 'quoteAssetPrecision', 'baseCommissionPrecision', 'quoteCommissionPrecision', 'orderTypes',
        ];
    }


    // Для построения формы
    public function form_bilder(){
        return [
            'exchange_id' => [
                Exchange::select('id', 'name')->pluck('name', 'id')->prepend('-', '')->toArray(),
                [
                    'class' => 'form-control form-select form-select-solid',
                    'prompt' => self::attr()['exchange_id'],
                    'data-kt-select2' => 'true',
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                ],
            ],
            'sell_currency' => 'text',
            'buy_currency' => 'text',
        ];
    }

    // Для построения формы
    public function form_bilderS(){
        return [
            'exchange_id' => 'text',
            'sell_currency' => 'text',
            'buy_currency' => 'text',

        ];
    }

    public function exchangeid()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }

    function formatFiltersData() {
        $jsonString=$this->orderTypes;
        $data = json_decode($jsonString, true);

        if (!$data) {
            return "Invalid JSON format.";
        }

        $formattedData = "";

        foreach ($data as $filter) {
            $formattedData .= "Filter Type: {$filter['filterType']}\n";

            switch ($filter['filterType']) {
                case 'PRICE_FILTER':
                    $formattedData .= "  Max Price: {$filter['maxPrice']}\n";
                    $formattedData .= "  Min Price: {$filter['minPrice']}\n";
                    $formattedData .= "  Tick Size: {$filter['tickSize']}\n";
                    break;
                case 'LOT_SIZE':
                    $formattedData .= "  Max Quantity: {$filter['maxQty']}\n";
                    $formattedData .= "  Min Quantity: {$filter['minQty']}\n";
                    $formattedData .= "  Step Size: {$filter['stepSize']}\n";
                    break;
                case 'ICEBERG_PARTS':
                    $formattedData .= "  Limit: {$filter['limit']}\n";
                    break;
                case 'MARKET_LOT_SIZE':
                    $formattedData .= "  Max Quantity: {$filter['maxQty']}\n";
                    $formattedData .= "  Min Quantity: {$filter['minQty']}\n";
                    $formattedData .= "  Step Size: {$filter['stepSize']}\n";
                    break;
                case 'TRAILING_DELTA':
                    $formattedData .= "  Max Trailing Above Delta: {$filter['maxTrailingAboveDelta']}\n";
                    $formattedData .= "  Max Trailing Below Delta: {$filter['maxTrailingBelowDelta']}\n";
                    $formattedData .= "  Min Trailing Above Delta: {$filter['minTrailingAboveDelta']}\n";
                    $formattedData .= "  Min Trailing Below Delta: {$filter['minTrailingBelowDelta']}\n";
                    break;
                case 'PERCENT_PRICE_BY_SIDE':
                    $formattedData .= "  Average Price Minutes: {$filter['avgPriceMins']}\n";
                    $formattedData .= "  Ask Multiplier Up: {$filter['askMultiplierUp']}\n";
                    $formattedData .= "  Bid Multiplier Up: {$filter['bidMultiplierUp']}\n";
                    $formattedData .= "  Ask Multiplier Down: {$filter['askMultiplierDown']}\n";
                    $formattedData .= "  Bid Multiplier Down: {$filter['bidMultiplierDown']}\n";
                    break;
                case 'NOTIONAL':
                    $formattedData .= "  Max Notional: {$filter['maxNotional']}\n";
                    $formattedData .= "  Min Notional: {$filter['minNotional']}\n";
                    $formattedData .= "  Average Price Minutes: {$filter['avgPriceMins']}\n";
                    $formattedData .= "  Apply Max To Market: {$filter['applyMaxToMarket']}\n";
                    $formattedData .= "  Apply Min To Market: {$filter['applyMinToMarket']}\n";
                    break;
                case 'MAX_NUM_ORDERS':
                    $formattedData .= "  Max Number of Orders: {$filter['maxNumOrders']}\n";
                    break;
                case 'MAX_NUM_ALGO_ORDERS':
                    $formattedData .= "  Max Number of Algo Orders: {$filter['maxNumAlgoOrders']}\n";
                    break;
                default:
                    $formattedData .= "  Unknown filter type\n";
            }

            $formattedData .= "\n";
        }

        return $formattedData;
    }
}
