<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCompetitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_competition', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('registerId')->constrained('competition_registration', 'id')->onDelete('cascade');
            $table->foreignId('transactionId')->constrained('transactions', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_competition');
    }
}
