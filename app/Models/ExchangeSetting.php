<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExchangeSetting extends Model
{
    use HasFactory;
    protected $table = 'exchange_settings'; // Имя таблицы

    protected $fillable = [
        'exchange_id', 'client_id', 'api_key', 'secret_key', 'status', 'name', 'purchase_commission', 'sale_commission'
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
            'title' => "Accounts", // Название страницы
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
            'client_id' => "Client",
            'api_key' => "Api key",
            'secret_key' => "Secret key",
            'status' => "Status",
            'name' => 'Name',
            'purchase_commission' => 'Maker',
            'sale_commission' => 'Taker'


        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable()
    {
        if (Auth::user()->type=='1') {
            return [
                'name','exchange_id', 'status', 'purchase_commission', 'sale_commission'
            ];
        }
        return [
            'name','exchange_id', 'client_id', 'status', 'purchase_commission', 'sale_commission'
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
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                    ],
                ],
                'api_key' => 'text',
                'secret_key' => 'text',
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
            'api_key' => 'text',
            'secret_key' => 'text',
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
                        'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                    ],
                ],

            ];
        }
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
                    'data-kt-data-dropdown-parent' => '#kt_menu_61484bf44f851'
                ],
            ],

        ];
    }

    public function getStatus(){
        return [
            0 => "Waiting",
            1 => "Work",
            2 => "Error",
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

    public function walletBalances()
    {
        return $this->hasMany(WalletBalance::class);
    }
}
