<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            //attributes below
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->timestamp('begin');
            $table->timestamp('end');
            $table->integer('section_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('section_id')->references('id')
            ->on('sections')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('events');
    }
}
