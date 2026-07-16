<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text("content")->nullable();
            $table->enum("media_type", ["image", "video"])->nullable();
            $table->string("media_url", 255)->nullable();
            $table->foreignId("user_id")->constrained("users")->onDelete("restrict");
            $table->foreignId("post_id")->nullable()->constrained("posts")->onDelete("restrict");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
