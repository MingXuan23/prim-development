<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->double('price', 8, 2)->nullable();
            $table->string('dish_image')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('organ_id');
            $table->unsignedBigInteger('dish_type');
            $table->foreign('organ_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('dish_type')->references('id')->on('dish_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dishes');
    }
}
