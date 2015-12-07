<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('point_id')->unsigned();
            $table->timestamps();

             $table->foreign('point_id')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');
        });

        Schema::create('hotel_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hotel_id')->unsigned();            
            $table->integer('user_id')->unsigned();
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();

             $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

             $table->foreign('hotel_id')
                ->references('id')
                ->on('hotels')
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
        Schema::drop('hotel_rating');
        Schema::drop('hotels');
        
    }
}
