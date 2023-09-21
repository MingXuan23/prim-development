<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableBusBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bus'); // Foreign key column
            $table->unsignedBigInteger('id_user'); // Foreign key column
            $table->text('book_date');
            $table->text('status');
            $table->foreign('id_bus')->references('id')->on('buses'); // Create foreign key
            $table->foreign('id_user')->references('id')->on('users'); // Create foreign key
            $table->unsignedBigInteger('transactionid')->nullable()->index();
            $table->foreign('transactionid')->nullable()->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_bookings');
    }
}
