<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKoopProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('koop_product_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('koop_id')->index();
            $table->unsignedBigInteger('product_type_id')->index();

            $table->foreign('koop_id')->references('id')->on('koop')->onDelete('cascade');
            $table->foreign('product_type_id')->references('id')->on('product_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('koop_product_type', function(Blueprint $table)
        {
            $table->drop('koop_id');
            $table->drop('product_type_id');
        });
    }
}
