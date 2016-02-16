<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function(Blueprint $table)
		{   //TODO
            // add foreign key to iterinary table
			$table->increments('id');
			$table->integer('day_id')->unsigned();
			$table->time('start_time');
			$table->time('end_time');

			/**
			 * required for morph to many
			 * 
			 * */
			$table->string('typable_type');
			$table->integer('typable_id')->unsigned();
			/**
			 * syntax : to get activity type
			 *  $activity->typable 
			 * 
			**/
			$table->timestamps();
			
			$table->foreign('day_id')
			->references('id')
			->on('days')
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
		Schema::drop('activities');
	}

}
