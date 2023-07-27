<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnInOrganizationChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_charges', function (Blueprint $table) {
            $table->renameColumn('minimun_amount', 'minimum_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_charges', function (Blueprint $table) {
            //
            $table->renameColumn('minimum_amount', 'minimun_amount');

        });
    }
}
