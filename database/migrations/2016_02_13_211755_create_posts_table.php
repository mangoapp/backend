<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts',function(Blueprint $table) {
            $table->increments('id');
            $table->text('body');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('thread_id')->unsigned();
            $table->foreign('thread_id')->references('id')->on('threads')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('reply_id')->unsigned();


            $table->boolean('anonymous');

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
