<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('trade_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bot_id')->nullable();
            $table->unsignedBigInteger('exchange_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->json('currency_pairs')->nullable();
            $table->decimal('predicted_profit_percent', 10, 2)->default(0.00);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->json('prices')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_history');
    }
}
