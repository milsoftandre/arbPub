<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    use HasFactory;

    protected $fillable = ['exchange_settings_id', 'asset', 'free', 'locked'];

    public function exchangeSetting()
    {
        return $this->belongsTo(ExchangeSetting::class);
    }
}