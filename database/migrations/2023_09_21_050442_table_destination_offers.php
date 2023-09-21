<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableDestinationOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_offers', function (Blueprint $table) {
            $table->id();
            $table->text('destination_name');
            $table->text('pick_up_point');
            $table->text('price_destination');
            $table->text('status');
            $table->text('available_time');
            $table->unsignedBigInteger('id_grab_student'); // Foreign key column
            $table->foreign('id_grab_student')->references('id')->on('grab_students'); // Create foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destination_offers');
    }
}
