<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('section_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')->onDelete('cascade');
                       
            $table->foreign('role_id')->references('id')->on('roles')
            ->onUpdate('cascade')->onDelete('cascade');

             $table->foreign('section_id')->references('id')->on('sections')
            ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id', 'section_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_user');
    }
}
