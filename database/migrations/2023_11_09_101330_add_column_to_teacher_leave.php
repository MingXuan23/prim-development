<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTeacherLeave extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_leave', function (Blueprint $table) {
            //

            $table->unsignedBigInteger('leave_type_id')->nullable();
            //$table->string('confirmation')->nullable();
            //$table->boolean('status');

            //$table->foreign('schedule_subject_id')->references('id')->on('schedule_subject')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_type')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_leave', function (Blueprint $table) {
            //
        });
    }
}
