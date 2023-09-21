<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableBusNotifys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bus_notifys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bus'); // Foreign key column
            $table->unsignedBigInteger('id_user'); // Foreign key column
            $table->text('status');
            $table->text('time_notify');
            $table->foreign('id_bus')->references('id')->on('buses'); // Create foreign key
            $table->foreign('id_user')->references('id')->on('users'); // Create foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_notifys');
    }
}
