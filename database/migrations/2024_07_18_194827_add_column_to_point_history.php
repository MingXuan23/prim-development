<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPointHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_history', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('member_id')->nullable();
            
            $table->foreign('member_id')->references('id')->on('referral_code_member')->onDelete('set null');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('point_history', function (Blueprint $table) {
            //
        });
    }
}
