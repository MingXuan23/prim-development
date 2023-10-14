<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ParcelDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('parcel_delivery', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('item_name');
        //     $table->double('weight');
        //     $table->string('receiver_postcode')->nullable();
        //     $table->string('sender_postcode')->nullable();
        //     $table->string('receiver_address');
        //     $table->string('sender_address');
        //     $table->foreign('pgng_order_id')->references('id')->on('pgng_orders')->onDelete('cascade');
        //     $table->foreign('price_id')->references('id')->on('parcel_delivery_price')->onDelete('cascade');
        //     $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade')->nullable();
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->nullable();
        //     $table->foreign('parcel_delivery_company')->references('id')->on('organization_id')->onDelete('cascade')->nullable();
        //     $table->timestamps('');
        //     $table->softDeletes();

        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcel_delivery');
    }
}
