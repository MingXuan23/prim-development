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
            $table->unsignedBigInteger('queue_id')->index();

            $table->foreign('product_item_id')->references('id')->on('product_item')->onDelete('cascade');
            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_queue', function(Blueprint $table)
        {
            $table->drop('product_item_id');
            $table->drop('queue_id');
        });
    }
}
