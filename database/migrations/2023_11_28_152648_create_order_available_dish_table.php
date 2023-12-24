<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAvailableDishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_available_dish', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->double('totalprice', 8, 2);
            $table->string('delivery_status');
            $table->string('delivery_proof_pic')->nullable();
            $table->string('order_desc')->nullable();
            $table->unsignedBigInteger('order_available_id');
            $table->unsignedBigInteger('order_cart_id');

            $table->foreign('order_available_id')->references('id')->on('order_available')->onDelete('cascade');
            $table->foreign('order_cart_id')->references('id')->on('order_cart')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_available_dish');
    }
}
