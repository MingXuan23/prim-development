<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePgngOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pgng_orders', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->timestamps();
            $table->string('order_type')->index()->nullable();
            $table->dateTime('pickup_date')->index()->nullable();
            $table->dateTime('delivery_date')->index()->nullable();
            $table->double('total_price', 8, 2)->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->mediumText('note')->nullable();
            $table->string('status')->index();
            $table->softDeletes();
            $table->dateTime('expired_at')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('organization_id')->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('transaction_id')->nullable()->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pgng_orders', function(Blueprint $table)
        {
            $table->drop('user_id');
            $table->drop('organization_id');
            $table->drop('transaction_id');
        });
    }
}
