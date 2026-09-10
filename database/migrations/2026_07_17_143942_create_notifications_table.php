<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string("content");
            $table->timestamps();
            $table->json("data")->nullable();
            $table->string("source_name");
            $table->bigInteger("source_id", false, true);
            $table->foreignId("user_id")->constrained("users")->onDelete("restrict");

            $table->index(["source_name", "source_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
