<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrganizationHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_hours', function (Blueprint $table) {
            $table->boolean('date_selection_enable')->default(true);
            $table->string('note_requirement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_hours', function (Blueprint $table) {
            // Schema::table('organization_hours', function (Blueprint $table) {
            // Reverse the changes in the down method (if needed)
            $table->dropColumn('date_selection_enable');
            $table->dropColumn('note_requirement');
    
        });
    }
}
