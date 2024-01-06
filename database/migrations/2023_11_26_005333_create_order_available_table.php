<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAvailableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_available', function (Blueprint $table) {
            $table->id();
            $table->dateTime('open_date');
            $table->dateTime('close_date');
            $table->dateTime('delivery_date');
            $table->string('delivery_address');
            $table->integer('quantity');
            $table->double('discount')->nullable();
            $table->unsignedBigInteger('dish_id');
            
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_available');
    }
}
