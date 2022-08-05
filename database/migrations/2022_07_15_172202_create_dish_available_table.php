<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishAvailableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dish_available', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->text('delivery_address')->nullable();

            $table->unsignedBigInteger('dish_id');
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dish_available');
    }
}
