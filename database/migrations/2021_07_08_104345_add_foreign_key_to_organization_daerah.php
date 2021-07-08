<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToOrganizationDaerah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_daerah', function (Blueprint $table) {
            $table->foreign('organization_negeri_id', 'organization_negeri_ibfk_1')->references('id')->on('organization_negeri')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_daerah', function (Blueprint $table) {
            $table->dropForeign('organization_negeri_ibfk_1');
        });
    }
}
