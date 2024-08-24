<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_token', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('remember_token')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->unsignedBigInteger('application_id')->nullable();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_token');
    }
}
