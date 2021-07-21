<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToStudentFeesNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_fees_new', function (Blueprint $table) {
            $table->foreign('Yuran_id', 'student_fees_new_ibfk_1')->references('id')->on('fees_new')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('KM_id', 'student_fees_new_ibfk_2')->references('id')->on('class_student')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_fees_new', function (Blueprint $table) {
            $table->dropForeign('student_fees_new_ibfk_1');
            $table->dropForeign('student_fees_new_ibfk_2');
        });
    }
}
