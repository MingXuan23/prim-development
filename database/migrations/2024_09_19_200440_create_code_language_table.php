<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodeLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_language', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true); // Added default value
            $table->decimal('price_weight', 8, 2)->default(1); // Defined precision and scale for decimal
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('code_language');
    }
}
