<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->smallInteger('type')->default(0);
            $table->double('balance')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });


        // Add admin
        DB::table('employees')->insert([
            [
                'name' => 'Администратор',
                'email' => 'admin',
                'password' => bcrypt('123456'),
                'type' => '0'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
