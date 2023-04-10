<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToFeesTransactionsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees_transactions_new', function (Blueprint $table) {
            $table->foreign('student_fees_id', 'transactions_fees_new_ibfk_1')->references('id')->on('student_fees_new')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('payment_type_id', 'transactions_fees_new_ibfk_2')->references('id')->on('payment_type')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('transactions_id', 'transactions_fees_new_ibfk_3')->references('id')->on('transactions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_transactions_new', function (Blueprint $table) {
            $table->dropForeign('transactions_fees_new_ibfk_1');
            $table->dropForeign('transactions_fees_new_ibfk_2');
            $table->dropForeign('transactions_fees_new_ibfk_3');
        });
    }
}
