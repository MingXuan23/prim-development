<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();

            $table->unsignedBigInteger('class_organization_id');
            $table->foreign('class_organization_id')->references('id')->on('class_organization')->onDelete('cascade');

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
        Schema::dropIfExists('class_fees', function (Blueprint $table) {
            $table->drop('class_organization_id');

            $table->drop('fees_id');
        });
    }
}
