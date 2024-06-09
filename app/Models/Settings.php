<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    protected $table = 'settings';

    protected $fillable = [
        'currency'
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
            'title' => __('settings.name'), // Название страницы
            'buttons' => [ // Кнопки для вюхи
                'show'=> __('settings.bshow'),
                'add'=> __('settings.badd'),
                'edit'=> __('settings.bedit'),
                'search'=> __('settings.bsearch'),
                'clear'=> __('settings.bclear'),
                'del'=> __('settings.bdel'),

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
            'form' => self::form_bilder()

        ];
    }

    /**
     * Поля и их атрибуты
     * @return array
     */
    public function attr()
    {
        return [
            'currency' => __('settings.currency'),

        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        return [
            'currency'
        ];
    }


    // Для построения формы
    public function form_bilder(){
        return [
            'currency' => 'text',

        ];
    }
}
