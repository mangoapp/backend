<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll', function (Blueprint $table) {
            //attributes below
            $table->increments('id');
            $table->string('answer');
            $table->boolean('status');
            $table->timestamps();
            $table->string('description');

            // //this creates the "column"
            $table->integer('section_id')->unsigned();
            //this is the id pointing
            $table->foreign('section_id')->references('id')
            ->on('sections')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll');
    }
}
