<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUserDonationReminder extends Migration
{
    /**s
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_donation_reminder', function (Blueprint $table) {
            $table->foreign('user_id', 'user_donation_reminder_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('donation_id', 'user_donation_reminder_ibfk_2')->references('id')->on('donations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('reminder_id', 'user_donation_reminder_ibfk_3')->references('id')->on('donation_reminder')->onUpdate('CASCADE')->onDelete('CASCADE');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_donation_reminder', function (Blueprint $table) {
			$table->dropForeign('user_donation_reminder_ibfk_1');
			$table->dropForeign('user_donation_reminder_ibfk_2');
			$table->dropForeign('user_donation_reminder_ibfk_3');
            
        });
    }
}
