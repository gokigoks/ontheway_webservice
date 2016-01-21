<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitySpotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_spot', function(Blueprint $table)
		{
			$table->integer('activity_id')->unsigned();
			$table->integer('spot_id')->unsigned();
			
			$table->foreign('activity_id')
			->references('id')
			->on('activities')
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
		Schema::drop('activity_spot');
	}

}
