<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_item_id')->index();
            $table->time('start_slot_time')->nullable();
            $table->time('end_slot_time')->nullable();
            $table->integer('quantity_available')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();

            $table->foreign('product_item_id')->references('id')->on('product_item')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_queue', function (Blueprint $table) {
            $table->dropColumn('product_item_id');
        });
    }
}
