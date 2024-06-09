<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logging extends Model
{
    use HasFactory;
    protected $table = 'loggings'; // Имя таблицы

    protected $fillable = [
        'user_id', 'what_changed', 'old_value', 'new_value'
    ];
}
