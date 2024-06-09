<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsTable extends Migration
{
    public function up()
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->unsignedBigInteger('exchange_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->json('currency_pairs')->nullable();
            $table->decimal('min_profit_percent', 10, 2)->default(0.00);
            $table->decimal('min_amount', 10, 2)->default(0.00);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bots');
    }
}
