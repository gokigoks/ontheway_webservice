<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestSpotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interest_spot', function(Blueprint $table)
		{
			$table->integer('spot_id')->unsigned();
			$table->integer('interest_id')->unsigned();

			$table->foreign('spot_id')
			->references('id')
			->on('spots')
			->onDelete('cascade');

			$table->foreign('interest_id')
			->references('id')
			->on('interests')
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
		Schema::table('interest_spot', function(Blueprint $table)
		{
			//
		});
	}

}
