<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateLoggingsTable extends Migration
{
    public function up()
    {
        Schema::create('loggings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('what_changed');
            $table->text('old_value');
            $table->text('new_value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loggings');
    }
}

