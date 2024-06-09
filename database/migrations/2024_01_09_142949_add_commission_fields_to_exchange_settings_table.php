<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionFieldsToExchangeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchange_settings', function (Blueprint $table) {
            $table->text('purchase_commission')->nullable();
            $table->text('sale_commission')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchange_settings', function (Blueprint $table) {
            $table->dropColumn('purchase_commission');
            $table->dropColumn('sale_commission');
        });
    }
}
