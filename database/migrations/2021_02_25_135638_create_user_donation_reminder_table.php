<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDonationReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_donation_reminder', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index('user_donation_reminder_ibfk_1');
            $table->bigInteger('donation_id')->unsigned()->index('user_donation_reminder_ibfk_2');
            $table->bigInteger('reminder_id')->unsigned()->index('user_donation_reminder_ibfk_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_donation_reminder');
    }
}
