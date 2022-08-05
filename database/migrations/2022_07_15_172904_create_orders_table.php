<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_status')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organ_id');
            $table->unsignedBigInteger('dish_available_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->double('delivery_latitude')->nullable();
            $table->double('delivery_longitude')->nullable();
            $table->string('order_description')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organ_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('dish_available_id')->references('id')->on('dish_available')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
