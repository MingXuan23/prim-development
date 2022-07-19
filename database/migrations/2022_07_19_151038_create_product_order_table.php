<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity')->nullable();
            $table->integer('status')->nullable();
            $table->unsignedBigInteger('product_item_id')->index();
            $table->unsignedBigInteger('koop_order_id')->index();

            $table->foreign('product_item_id')->references('id')->on('product_item')->onDelete('cascade');
            $table->foreign('koop_order_id')->references('id')->on('koop_order')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order');
    }
}
