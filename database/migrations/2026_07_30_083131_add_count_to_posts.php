<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('likes_count')->after('media_url')->default(0);
            $table->integer('comments_count')->after('likes_count')->default(0);
            $table->integer('shares_count')->after('comments_count')->default(0);

            // used for tracking the root post for nested shared
            $table->foreignId('root_shared_post_id')->nullable()->after('shared_post_id')->constrained('posts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['root_shared_post_id']);
            $table->dropColumn([
                'likes_count',
                'comments_count',
                'shares_count',
                'root_shared_post_id',
            ]);
        });
    }
}
