<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('status')->nullable();
            $table->double('totalamount', 8, 2)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('yearfees_id');
            $table->foreign('yearfees_id')->references('id')->on('year_fees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees', function (Blueprint $table)
        {
            $table->drop('yearfees_id');
        });
    }
}
