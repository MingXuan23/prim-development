<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToStudentOutingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_outing', function (Blueprint $table) {
            //
            $table->date('apply_date_time');
            $table->datetime('arrive_date_time')->nullable();
            $table->integer('status');
            $table->bigInteger('classification_id')->unsigned();
            $table->foreign('classification_id')->references('id')->on('classifications')->onDelete('cascade');
            $table->bigInteger('warden_id')->unsigned()->nullable();
            $table->foreign('warden_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('guard_id')->unsigned()->nullable();
            $table->foreign('guard_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_outing', function (Blueprint $table) {
            //
        });
    }
}
