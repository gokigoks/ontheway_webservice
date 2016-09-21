<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        Schema::create('food_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');            
            $table->timestamps();
        });

        Schema::create('eats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');            
            $table->integer('point_id')->unsigned();
            $table->timestamps();

             $table->foreign('point_id')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');
        });

        Schema::create('eat_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eat_id')->unsigned();            
            $table->integer('user_id')->unsigned();
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();

             $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

             $table->foreign('eat_id')
                ->references('id')
                ->on('eats')
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
        Schema::drop('eat_ratings');
        Schema::drop('eats');
        Schema::drop('food_category');
    }
}
