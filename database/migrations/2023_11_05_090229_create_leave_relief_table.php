<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveReliefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_relief', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_leave_id');
            $table->foreign('teacher_leave_id')->references('id')->on('teacher_leave')->onDelete('cascade');

            $table->unsignedBigInteger('schedule_subject_id');
            $table->foreign('schedule_subject_id')->references('id')->on('schedule_subject')->onDelete('cascade');

            $table->unsignedBigInteger('replace_teacher_id')->nullable();
            $table->foreign('replace_teacher_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('confirmation')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_relief');
    }
}
