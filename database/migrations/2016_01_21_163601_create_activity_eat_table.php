<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityEatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_eat', function(Blueprint $table)
		{
			$table->integer('activity_id')->unsigned();
			$table->integer('eat_id')->unsigned();
			
			$table->foreign('activity_id')
			->references('id')
			->on('activities')
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
		Schema::drop('activity_eat');
	}

}
