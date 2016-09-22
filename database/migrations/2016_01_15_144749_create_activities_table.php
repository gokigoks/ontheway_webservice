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
		{
			$table->increments('id');
			$table->integer('day')->unsigned()->default(1);
			$table->integer('iterinary_id')->unsigned();
			$table->string('start_time');
			$table->string('end_time');

			/**
			 * required for morph to many
			 * typable refers to spot,eat,and transport
			 * */
			$table->string('typable_type');
			$table->integer('typable_id')->unsigned();
			/**
			 * syntax : to get activity type
			 *  $activity->typable 
			 * 
			**/
			$table->timestamps();


			$table->foreign('iterinary_id')
			->references('id')
			->on('iterinaries')
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
