<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNameToGrabstudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grab_students', function (Blueprint $table) {
            $table->unsignedBigInteger('transactionid')->nullable()->index();
            $table->foreign('transactionid')->nullable()->references('id')->on('transactions')->onDelete('cascade');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grab_students', function (Blueprint $table) {
            $table->dropColumn('transactionid');
        });
    }
}
