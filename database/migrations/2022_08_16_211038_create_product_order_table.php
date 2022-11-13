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
            $table->integer('quantity');
            $table->integer('selling_quantity');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('product_item_id')->index();
            $table->unsignedBigInteger('pgng_order_id')->index();

            $table->foreign('product_item_id')->references('id')->on('product_item')->onDelete('cascade');
            $table->foreign('pgng_order_id')->references('id')->on('pgng_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order', function (Blueprint $table) {
            $table->dropColumn('product_item_id');
            $table->dropColumn('pgng_order_id');
        });
    }
}
