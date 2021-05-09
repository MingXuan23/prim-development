<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->date('date_created')->nullable();
            $table->date('date_started')->nullable();
            $table->date('date_end')->nullable();
            $table->string('status')->nullable();
            $table->string('tax_payer')->nullable();
            $table->float('total_tax')->nullable();
            $table->string('donation_poster')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
