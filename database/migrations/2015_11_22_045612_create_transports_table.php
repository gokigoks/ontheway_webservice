<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        

        Schema::create('transports', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('travel_time');
            $table->integer('price');
            $table->enum('mode',array('walking','train','taxi','rented_vehicle','plane','boat','bus'));
            $table->integer('point_of_origin')->unsigned();
            $table->integer('point_of_destination')->unsigned();
            $table->timestamps();

             $table->foreign('point_of_origin')
                ->references('id')
                ->on('points')
                ->onDelete('cascade');

             $table->foreign('point_of_destination')
                ->references('id')
                ->on('points')
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
        Schema::drop('transports');
    }
}
