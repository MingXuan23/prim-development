<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTransactionsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_transactions_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_fees_id');
            $table->unsignedBigInteger('payment_type_id');
            $table->unsignedBigInteger('transactions_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_transactions_new');
    }
}
