<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrganizationUserStudentPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_user_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('organization_user_id')->index();
            $table->foreign('organization_user_id')->references('id')->on('organization_user')->onDelete('cascade');

            $table->unsignedBigInteger('student_id')->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_user_student', function (Blueprint $table)
        {
            $table->drop('organization_user_id');

            $table->drop('student_id');

        });
    }
}
