<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToClassStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_student', function (Blueprint $table) {
            //
            $table->integer('outing_status')->nullable();
            $table->integer('blacklist')->nullable();
            $table->datetime('start_date_time')->nullable();
            $table->datetime('end_date_time')->nullable();
            $table->bigInteger('dorm_id')->unsigned()->nullable()->after('end_date_time');
            $table->foreign('dorm_id')->references('id')->on('dorms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_student', function (Blueprint $table) {
            //
            $table->dropForeign(['dorm_id']);

            $table->dropColumn(['outing_status', 'blacklist', 'start_date_time', 'end_date_time', 'dorm_id']);
        });
    }
}
