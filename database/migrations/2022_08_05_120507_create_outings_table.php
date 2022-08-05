<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outings', function (Blueprint $table) {
            $table->id();
<<<<<<<< HEAD:database/migrations/2022_08_05_121130_create_outings_table.php
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
========
            $table->datetime('start_date_time');
            $table->datetime('end_date_time');
>>>>>>>> ba6334911aed22cbb5031b8b56b7c5143cba73aa:database/migrations/2022_08_05_120507_create_outings_table.php
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outings');
    }
}
