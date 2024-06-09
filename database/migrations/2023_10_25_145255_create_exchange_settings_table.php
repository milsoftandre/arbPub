<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('exchange_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('api_key')->default('');
            $table->string('secret_key')->default('');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exchange_settings');
    }
}
