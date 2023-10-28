<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions',function($table){
            $table->float('discount',5,2)->default(0)->change();
            $table->dropForeign('promotions_homestayid_foreign');
            $table->dropColumn('homestayid');
            $table->enum('promotion_type',['discount','increase']);
            $table->float('increase',5,2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('homestay_id');
            $table->foreign('homestay_id')->references('roomid')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
