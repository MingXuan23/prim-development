<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mesej')->nullable();

            $table->unsignedBigInteger('chatroom_id');
            $table->foreign('chatroom_id')->references('id')->on('chatrooms')->onDelete('cascade');

            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('message_type')->onDelete('cascade');

            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('send_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->string('delivered_status')->nullable();
            $table->string('seen_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments', function (Blueprint $table)
        {
            $table->drop('chatroom_id');

            $table->drop->nullable()('type_id');
        });
    }
}
