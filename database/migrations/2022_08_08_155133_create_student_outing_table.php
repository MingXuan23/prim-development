<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentOutingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_outing', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->datetime('out_date_time')->nullable();
            $table->datetime('in_date_time')->nullable();
            $table->timestamps();
            $table->bigInteger('outing_id')->unsigned()->nullable();
            $table->bigInteger('class_student_id')->unsigned();
            $table->foreign('outing_id')->references('id')->on('outings')->onDelete('cascade');
            $table->foreign('class_student_id')->references('id')->on('class_student')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_outing');
    }
}
