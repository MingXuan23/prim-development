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
            $table->datetime('in_date_time')->nullable();
            $table->datetime('out_date_time')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('outing_id');
            $table->unsignedBigInteger('class_student_id');
            $table->foreign('outing_id')->references('id')->on('outing')->onDelete('cascade');
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
