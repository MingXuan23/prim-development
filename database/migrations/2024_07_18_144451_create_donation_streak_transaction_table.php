<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationStreakTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_streak_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_streak_id');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();
            $table->integer('day');
            $table->boolean('quality_donation');

            $table->foreign('donation_streak_id')->references('id')->on('donation_streak')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_streak_transaction');
    }
}
