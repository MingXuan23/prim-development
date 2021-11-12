<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToClassOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class_organization', function (Blueprint $table) {
            $table->unsignedBigInteger('organ_user_id')->nullable();
            $table->foreign('organ_user_id')->nullable()->references('id')->on('organization_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('class_organization', function (Blueprint $table) {
            $table->dropForeign('organ_user_id');
        });
    }
}
