<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstagramPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pk')->index();
            $table->string('user_pk')->index();
            $table->tinyInteger('media_type');
            $table->string('image_versions', 1000);
            $table->string('video_versions', 1000);
            $table->string('code');
            $table->integer('view_count');
            $table->integer('comment_count');
            $table->integer('like_count');
            $table->text('caption');
            $table->timestamp('taken_at')->nullable();
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
        Schema::drop('instagram_posts');
    }
}
