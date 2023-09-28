<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableGrabStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grab_students', function (Blueprint $table) {
            $table->id();
            $table->text('car_brand');
            $table->text('car_name');
            $table->text('car_registration_num');
            $table->integer('number_of_seat');
            $table->text('available_time');
            $table->text('status');
            $table->unsignedBigInteger('id_organizations'); // Foreign key column
            $table->foreign('id_organizations')->references('id')->on('organizations'); // Create foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grab_students');
    }
}
