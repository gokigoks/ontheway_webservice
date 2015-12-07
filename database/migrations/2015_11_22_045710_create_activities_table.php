<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('iterinary_day_id')->unsigned();
            $table->integer('type_reference')->unsigned();
            $table->integer('duration_of_activity'); //
            $table->enum('activity_type', array('eat','spot','hotel','transportation'));                    
            $table->timestamps();

            $table->foreign('iterinary_day_id')
                ->references('id')
                ->on('iterinary_day')
                ->onDelete('cascade');
        });

        Schema::create('hotel_activity', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('user_id')->unsigned();
            $table->integer('hotel_id')->unsigned();
            $table->integer('price');
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();
        });

        Schema::create('eat_activity', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('user_id')->unsigned();
            $table->integer('hotel_id')->unsigned();
            $table->integer('price');
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();
        });

        Schema::create('transpo_activity', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('user_id')->unsigned();
            $table->integer('hotel_id')->unsigned();
            $table->integer('price');
            $table->enum('rating',array(1,2,3,4,5));
            $table->timestamps();
        });

        Schema::create('spots_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('hotel_id')->unsigned();
            $table->integer('price');            
            $table->enum('rating',array(1,2,3,4,5));
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
        Schema::drop('activities');
        Schema::drop('spots_activity');
        Schema::drop('transpo_activity');
        Schema::drop('eat_activity');
        Schema::drop('hotel_activity');
        
    }
}
