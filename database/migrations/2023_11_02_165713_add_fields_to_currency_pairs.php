<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCurrencyPairs extends Migration
{
    public function up()
    {
        Schema::table('currency_pairs', function (Blueprint $table) {
            $table->string('status')->default('TRADING');
            $table->integer('baseAssetPrecision')->default(8);
            $table->integer('quotePrecision')->default(8);
            $table->integer('quoteAssetPrecision')->default(8);
            $table->integer('baseCommissionPrecision')->default(8);
            $table->integer('quoteCommissionPrecision')->default(8);
            $table->json('orderTypes');

        });
    }

    public function down()
    {
        Schema::table('currency_pairs', function (Blueprint $table) {
            $table->dropColumn(['status', 'baseAssetPrecision', 'quotePrecision', 'quoteAssetPrecision', 'baseCommissionPrecision', 'quoteCommissionPrecision', 'orderTypes']);
        });
    }
}

