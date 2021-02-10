<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonationTransactionPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('donation_id')->index();
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
            $table->unsignedBigInteger('transaction_id')->index();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->unsignedBigInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donation_transaction', function (Blueprint $table)
        {
            $table->drop('donation_id');

            $table->drop('transaction_id');

            $table->drop('payment_type_id');
        });
    }
}
