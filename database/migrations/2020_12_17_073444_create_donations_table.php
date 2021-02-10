<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('description')->nullable();
            $table->string('amount')->nullable();
            $table->date('date_created')->nullable();

            // $table->integer('organ_id');
            $table->unsignedBigInteger('organ_id');
            $table->foreign('organ_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations', function (Blueprint $table)
        {
            $table->drop('organ_id');
        });
    }
}
