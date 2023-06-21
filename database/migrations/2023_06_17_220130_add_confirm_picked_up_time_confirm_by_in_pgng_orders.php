<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmPickedUpTimeConfirmByinPgngOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pgng_orders', function (Blueprint $table) {
            $table->dateTime('confirm_picked_up_time')->nullable();
            $table->unsignedBigInteger('confirm_by')->nullable()->index();

            $table->foreign('confirm_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pgng_orders', function (Blueprint $table) {
            $table->dropColumn('confirm_picked_up_time');
        });
    }
}
