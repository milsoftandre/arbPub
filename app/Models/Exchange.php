<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;
    protected $table = 'exchanges'; // Имя таблицы

    protected $fillable = [
        'name', 'sell_rate', 'buy_rate'
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
            'title' => "Exchanges", // Название страницы
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
            'sell_rate' => "Sell rate",
            'buy_rate' => "Buy rate",

        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        return [
            'name', 'sell_rate', 'buy_rate'
        ];
    }


    // Для построения формы
    public function form_bilder(){
        return [
            'name' => 'text',
            'sell_rate' => 'text',
            'buy_rate' => 'text',
        ];
    }

    // Для построения формы
    public function form_bilderS(){
        return [
            'name' => 'text',


        ];
    }
}
