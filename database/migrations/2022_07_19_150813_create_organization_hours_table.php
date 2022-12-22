<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('day');
            $table->time('open_hour')->nullable();
            $table->time('close_hour')->nullable();
            $table->integer('status');
            $table->timestamps();
            $table->unsignedBigInteger('organization_id')->index();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_hours', function(Blueprint $table)
        {
            $table->drop('organization_id');
        });
    }
}
