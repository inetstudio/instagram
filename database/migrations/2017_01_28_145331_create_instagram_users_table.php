<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstagramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pk')->index();
            $table->string('username');
            $table->string('full_name');
            $table->string('profile_pic_url', 1000);
            $table->integer('follower_count');
            $table->integer('following_count');
            $table->integer('media_count');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('instagram_users');
    }
}
