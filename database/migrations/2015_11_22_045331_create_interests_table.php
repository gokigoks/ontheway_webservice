<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('interest_name');
            $table->string('description');
            $table->integer('diversity_value');
            $table->timestamps();
        });

        Schema::create('user_interest', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('interest_id')->unsigned();            
            $table->timestamps();

              $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

              $table->foreign('interest_id')
                ->references('id')
                ->on('interests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_interest');
        Schema::drop('interests');
    }
}
