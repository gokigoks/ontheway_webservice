<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightIterinariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('flight_iterinaries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('segment_id')->unsigned();

			$table->timestamps();

			$table->foreign('segment_id')
			->references('id')
			->on('segments')
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
		Schema::drop('flight_iterinaries');
	}

}
