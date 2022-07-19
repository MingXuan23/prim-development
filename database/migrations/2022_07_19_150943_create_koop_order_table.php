<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKoopOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koop_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->date('pickup_date')->nullable();
            $table->integer('method_status')->nullable();
            $table->integer('status')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('koop_id')->index();

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
        Schema::dropIfExists('koop_order', function(Blueprint $table)
        {
            $table->drop('koop_id');
        });
    }
}
