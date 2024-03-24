<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('point_history', function (Blueprint $table) {
            // Make 'fromSubline' column nullable
            $table->boolean('fromSubline')->nullable()->change();
            
            // Add new 'desc' column
            $table->string('desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('point_history', function (Blueprint $table) {
            // Drop 'desc' column
            $table->dropColumn('desc');

            // Change 'fromSubline' column back to non-nullable
            $table->boolean('fromSubline')->nullable(false)->change();
        });
    }
}
