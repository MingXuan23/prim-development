<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asramas', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('status');
            // $table->integer('student_id')->unsigned();
            // $table->integer('teacher_id')->unsigned(); 
            // $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            // $table->foreign('teacher_id')->references('id')->on('organization_user')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asramas');
    }
}
