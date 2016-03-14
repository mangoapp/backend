<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score');
            $table->integer('user_id')->unsigned();
            $table->integer('section_id')->unsigned();
            $table->integer('assignment_id')->unsigned();
            $table->integer('quiz_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('section_id')->references('id')->on('sections')
                ->onUpdate('cascade')->onDelete('cascade');

             $table->foreign('assignment_id')->references('id')->on('assignments')
                 ->onUpdate('cascade')->onDelete('cascade');

//            $table->foreign('quiz_id')->references('id')->on('quizzes')
//                ->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('grades');
    }
}
