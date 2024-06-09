<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWalletBalancesPrecision extends Migration
{
    public function up()
    {
        Schema::table('wallet_balances', function (Blueprint $table) {
            $table->decimal('free', 18, 10)->change();
            $table->decimal('locked', 18, 10)->change();
        });
    }

    public function down()
    {
        Schema::table('wallet_balances', function (Blueprint $table) {
            $table->decimal('free', 15, 8)->change();
            $table->decimal('locked', 15, 8)->change();
        });
    }
}
