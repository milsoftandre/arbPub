<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelSettingsToBotsTable extends Migration
{
    public function up()
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->json('cancel_settings')->nullable();
        });
    }

    public function down()
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->dropColumn('cancel_settings');
        });
    }
}
