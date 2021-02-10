<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();

            $table->unsignedBigInteger('class_student_id');
            $table->foreign('class_student_id')->references('id')->on('class_student')->onDelete('cascade');

            $table->unsignedBigInteger('fees_details_id');
            $table->foreign('fees_details_id')->references('id')->on('fees_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_fees', function (Blueprint $table) {
            $table->drop('class_student_id');

            $table->drop('fees_details_id');
        });
    }
}
