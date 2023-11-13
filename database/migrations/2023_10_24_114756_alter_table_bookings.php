<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings',function($table){
            $table->integer('review_star')->nullable();
            $table->string('review_comment')->nullable();
            $table->decimal('discount_received',8,2)->default(0)->nullable();
            $table->decimal('increase_received',8,2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings',function($table){
            $table->dropColumn('review_star');
            $table->dropColumn('review_comment');
            $table->dropColumn('discount_received');
            $table->dropColumn('increase_received');
        });
    }
}
