<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('email')->unique();
            $table->string('nama')->nullable();
            $table->string('telno')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state')->nullable();
            $table->string('fixed_charges')->nullable();
            // $table->string('type_org')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('type_org');
            $table->foreign('type_org')->references('id')->on('type_organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations', function (Blueprint $table)
        {
            $table->drop('type_org');
        });
    }
}
