<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Bookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('bookingid');
            $table->date('checkin');
            $table->date('checkout');
            $table->string('status');
            $table->decimal('totalprice',6,2);
            $table->unsignedBigInteger('customerid')->index();
            $table->foreign('customerid')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('roomid')->index();
            $table->foreign('roomid')->references('roomid')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
