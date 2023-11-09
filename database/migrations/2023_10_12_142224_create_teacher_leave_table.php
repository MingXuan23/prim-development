<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_leave', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('schedule_subject_id')->nullable();
            $table->json('period')->nullable();
            $table->date('date');
            $table->string('desc')->nullable();
            $table->boolean('status');
            $table->unsignedBigInteger('teacher_id')->nullable();
            //$table->string('confirmation')->nullable();
            //$table->boolean('status');

            //$table->foreign('schedule_subject_id')->references('id')->on('schedule_subject')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_leave');
    }
}
