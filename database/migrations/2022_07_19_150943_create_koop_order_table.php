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
            $table->double('total_price', 8, 2)->nullable();
            $table->mediumText('note')->nullable();
            $table->integer('status')->nullable();
            $table->softDeletes();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('organization_id')->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('koop_order', function(Blueprint $table)
        {
            $table->drop('user_id');
            $table->drop('organization_id');
        });
    }
}
