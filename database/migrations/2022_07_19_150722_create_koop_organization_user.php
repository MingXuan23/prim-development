<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKoopOrganizationUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koop_organization_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organization_user_id')->index();
            $table->unsignedBigInteger('koop_id')->index();

            $table->foreign('organization_user_id')->references('id')->on('organization_user')->onDelete('cascade');
            $table->foreign('koop_id')->references('id')->on('koop')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('koop_organization_user', function (Blueprint $table)
        {
            $table->drop('organization_user_id');
            $table->drop('koop_id');
        });
    }
}
