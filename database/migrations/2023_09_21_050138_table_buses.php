<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableBuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->text('total_seat');
            $table->text('available_seat');
            $table->text('booked_seat');
            $table->text('minimum_seat');
            $table->text('bus_registration_number');
            $table->text('status');
            $table->text('trip_number');
            $table->text('trip_description');
            $table->text('bus_depart_from');
            $table->text('bus_destination');
            $table->text('departure_time');
            $table->text('price_per_seat');
            $table->text('estimate_arrive_time');
            $table->text('departure_date');
            $table->unsignedBigInteger('id_organizations'); // Foreign key column
            $table->foreign('id_organizations')->references('id')->on('organizations'); // Create foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buses');
    }
}
