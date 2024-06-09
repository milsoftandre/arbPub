<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name', 'email', 'password', 'type','balance','token',
    ];




    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Настройки модели
     * @return array
     */
    public static function settings($full=0,$type=0)
    {

        $title = __('employee.title1');

        if((strpos("-".Route::currentRouteName(),"hand"))){
            $title = __('employee.title2');
        }
        if((strpos("-".Route::currentRouteName(),"client"))){
            $title = (Auth::user()->type=='0')?'Users':__('employee.title3');
        }

        $arr = [
            'pl' => 30, // Записей на странице
            'isEdit' => true, // Возможность редактировать
            'isDel' => true, // Возможность удаления
            'isAdd' => true, // Возможность добавления
            'isShow' => false,
            'isCheckbox' => false,
            'isExport' => true,
            'isImport' => true,
            'page' => 'index', // Название страницы
            'title' => $title, // Название страницы
            'buttons' => [ // Кнопки для вюхи
                'show'=> __('employee.bshow'),
                'add'=> 'ADD',
                'edit'=> __('employee.bedit'),
                'search'=> __('employee.bsearch'),
                'clear'=> __('employee.bclear'),
                'del'=> __('employee.bdel'),

            ],
            'search_settings' => [  // Настройки для поиска
                'like' => [ // Поля которые используем с неточным совпадением
                    'name',
                    'pwd',
                    'email',
                    'tel',
                    'rules',
                    'type',
                    'created_at',
                    'bdate',
                    'adres'
                ],
                'range' => [ // Поля которые используем с периодом от-до
                    'oklad'
                ]
            ],
            'typeuser' => [
                0 => 'Физические лица',
                3 => 'Самозанятый ',
                1 => 'ИП',
                2 => 'ООО'
            ],
            'table' => self::showInTable(),
            'table_client' => self::showInTable(1),
            'attr' => self::attr(),
            'form' => []

        ];

        if($full==2){
            $arr['search'] = self::search_bilder();
        }elseif($full){
            $arr['form'] = self::form_bilder($type);
        }

        return $arr;
    }

    /**
     * Поля и их атрибуты
     * @return array
     */
    public function attr()
    {
        return [
            'name' => __('employee.fname'),
            'pwd' => __('employee.fpwd'),
            'email' => __('employee.femail'),
            'created_at' => __('employee.fcreated_at'),
            'balance' => 'Balance',
            'password' => __('employee.fpassword'),
            'token' => 'Status',
        ];
    }


    // Поля которые отображаем в таблице, в порядке отображения
    public function showInTable($type=0)
    {
        if ($type==1){
            return [
                'name',
                'email',
                'balance',
                'token',
                'created_at'
            ];
        }
        return [
            'name',
            'email',
            'created_at'
        ];
    }


    // Для построения формы
    public function form_bilder($type=0){
        if($type==0){
        return [
            'name' => 'text',
            'email' => 'text',
            'password' => 'password',


        ];
        }else {
            return [
                'name' => 'text',
                'email' => 'text',
                'password' => 'password',

            ];
        }
    }
    public function search_bilder(){
        return [];
    }

}
