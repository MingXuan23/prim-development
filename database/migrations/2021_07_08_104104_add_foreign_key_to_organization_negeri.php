<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToOrganizationNegeri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_negeri', function (Blueprint $table) {
            $table->foreign('organization_parent_id', 'organization_parent_ibfk_1')->references('id')->on('organization_parent')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_negeri', function (Blueprint $table) {
            $table->dropForeign('organization_parent_ibfk_1');
        });
    }
}
