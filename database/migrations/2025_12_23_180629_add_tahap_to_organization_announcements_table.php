<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTahapToOrganizationAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_announcements', function (Blueprint $table) {
            $table->enum('tahap', ['1', '2', 'both'])->default('both')->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_announcements', function (Blueprint $table) {
            $table->dropColumn('tahap');
        });
    }
}
