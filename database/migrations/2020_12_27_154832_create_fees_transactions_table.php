<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_transactions', function (Blueprint $table) {
            
            $table->bigIncrements('id');

            $table->unsignedBigInteger('student_fees_id');
            $table->foreign('student_fees_id')->references('id')->on('student_fees')->onDelete('cascade');

            $table->unsignedBigInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_type')->onDelete('cascade');

            $table->unsignedBigInteger('transactions_id');
            $table->foreign('transactions_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_transactions', function (Blueprint $table)
        {
            $table->drop('class_fees_id');

            $table->drop('payment_type_id');

            $table->drop('transactions_id');
        });
    }
}
