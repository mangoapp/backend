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
            // //this creates the "column"
            $table->integer('section_id')->unsigned();
            //this is the id pointing
            $table->foreign('section_id')->references('id')
            ->on('sections')->onUpdate('cascade')->onDelete('cascade');

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
