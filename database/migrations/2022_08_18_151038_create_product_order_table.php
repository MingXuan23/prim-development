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
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('product_item_id')->index();
            $table->unsignedBigInteger('pickup_order_id')->index();

            $table->foreign('product_item_id')->references('id')->on('product_item')->onDelete('cascade');
            $table->foreign('pickup_order_id')->references('id')->on('pickup_order')->onDelete('cascade');
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
            $table->dropColumn('pickup_order_id');
        });
    }
}
