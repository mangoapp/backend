<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PollResponse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_response', function (Blueprint $table) {
            $table->increments('id');
            $table->string('answer');
            $table->timestamps();

            // //this creates the "column"
            $table->integer('poll_id')->unsigned();
            //this is the id pointing
            $table->foreign('poll_id')->references('id')
            ->on('poll')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_response');
    }
}
