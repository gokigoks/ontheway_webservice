<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('spot_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');            
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('spots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('point_id')->unsigned();
            $table->integer('fee')->nullable();            
            $table->timestamps();
              $table->foreign('point_id')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');
        });
                
        Schema::create('spot_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('spot_id')->unsigned();            
            $table->integer('user_id')->unsigned();
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();

             $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
                
             $table->foreign('spot_id')
                ->references('id')
                ->on('spots')
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
        Schema::drop('spot_ratings');
        Schema::drop('spots');
        Schema::drop('spots_category');
    }
}
