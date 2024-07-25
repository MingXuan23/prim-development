<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCodeMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_code_member', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('leader_referral_code_id')->references('id')->on('referral_code');
            $table->unsignedBigInteger('member_user_id')->references('id')->on('users');
            $table->boolean('status')->nullable();
            $table->string('desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_code_member');
    }
}
