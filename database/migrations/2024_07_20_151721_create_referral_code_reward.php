<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCodeReward extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_code_reward', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->boolean('status');
            $table->integer('quantity');
            $table->json('condition')->nullable();
            $table->json('asset_to_use')->nullable();
            $table->text('additionInfo')->nullable();
            $table->string('external_link')->nullable();
            $table->boolean('payment');
            $table->decimal('paymentAmount', 10, 2)->default(0);
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_code_reward');
    }
}
