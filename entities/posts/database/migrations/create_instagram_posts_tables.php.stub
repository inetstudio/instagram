<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateInstagramPostsTables.
 */
class CreateInstagramPostsTables extends Migration
{
    /**
     * Run the migrations.

     */
    public function up()
    {
        Schema::create('instagram_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pk')->index();
            $table->string('user_pk')->index();
            $table->json('additional_info');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.

     */
    public function down()
    {
        Schema::drop('instagram_posts');
    }
}
