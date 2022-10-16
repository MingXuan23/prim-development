<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('slot_time');
            $table->integer('status');
            $table->integer('slot_number')->nullable();
            $table->unsignedBigInteger('product_group_id');
            $table->timestamps();
            
            $table->foreign('product_group_id')->references('id')->on('product_group')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queues', function(Blueprint $table)
        {
            $table->drop('product_group_id');
        });
    }
}
