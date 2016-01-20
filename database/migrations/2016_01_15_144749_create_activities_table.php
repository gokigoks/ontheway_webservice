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
			$table->integer('day_id')->unsigned();
			$table->time('start_time');
			$table->time('end_time');
			$table->enum('type',array('spot','eat','transport'));
			$table->integer('type_id')->unsigned();
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
