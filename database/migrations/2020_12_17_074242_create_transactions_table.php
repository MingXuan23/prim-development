<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('description')->nullable();
            $table->string('transac_no')->nullable();
            $table->dateTime('datetime_created')->nullable();   
            $table->double('amount', 8, 2)->nullable();
            $table->string('status')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('telno')->nullable();
            $table->string('username')->nullable();
            $table->string('fpx_checksum', 600)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
