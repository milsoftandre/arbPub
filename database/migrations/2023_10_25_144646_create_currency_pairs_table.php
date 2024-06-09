<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyPairsTable extends Migration
{
    public function up()
    {
        Schema::create('currency_pairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_id')->nullable();
            $table->string('sell_currency')->default('');
            $table->string('buy_currency')->default('');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currency_pairs');
    }
}
