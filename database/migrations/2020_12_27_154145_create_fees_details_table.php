<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();

            $table->unsignedBigInteger('details_id');
            $table->foreign('details_id')->references('id')->on('details')->onDelete('cascade');

            $table->unsignedBigInteger('fees_id');
            $table->foreign('fees_id')->references('id')->on('fees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_details', function (Blueprint $table)
        {
            $table->drop('details_id');

            $table->drop('fees_id');
        });
    }
}
