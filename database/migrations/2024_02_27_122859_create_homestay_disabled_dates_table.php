<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomestayDisabledDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homestay_disabled_dates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedBigInteger('homestay_id');
            $table->timestamps();
            $table->foreign('homestay_id')->references('roomid')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homestay_disabled_dates');
    }
}
