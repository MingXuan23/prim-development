<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesNewOrganizationUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_new_organization_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();
            
            $table->unsignedBigInteger('fees_new_id')->index();
            $table->foreign('fees_new_id')->references('id')->on('fees_new')->onDelete('cascade');
            $table->unsignedBigInteger('organization_user_id')->index();
            $table->foreign('organization_user_id')->references('id')->on('organization_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_new_organization_user');
    }
}
