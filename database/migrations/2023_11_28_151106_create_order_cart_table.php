<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cart', function (Blueprint $table) {
            $table->id();
            $table->string('order_status');
            $table->double('totalamount', 8, 2);
            $table->timestamps();
            $table->string('cart_desc')->nullable();
            // $table->unsignedBigInteger('user_id');
            $table->bigInteger('user_id');
            $table->bigInteger('organ_id');
            $table->unsignedBigInteger('transactions_id')->nullable();

            $table->foreign('transactions_id')->references('id')->on('transactions')->onUpdate('CASCADE')->onDelete('CASCADE');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_cart');
    }
}
