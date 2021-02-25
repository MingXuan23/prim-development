<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_reminder', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('timezoneoffset');
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned()->index('donation_reminder_ibfk_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_reminder');
    }
}
