<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassStudentPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('organclass_id')->index();
            $table->foreign('organclass_id')->references('id')->on('class_organization')->onDelete('cascade');

            $table->unsignedBigInteger('student_id')->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->integer('status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_student', function (Blueprint $table)
        {
            $table->drop('organclass_id');

            $table->drop('student_id');

        });
    }
}
