<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('segments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('route_id')->unsigned();
			$table->string('origin_name');
			$table->string('destination_name');
			$table->string('segment_origin');
			$table->string('segment_destination');
			$table->integer('price');
			$table->integer('distance');
			$table->integer('duration');
			$table->string('mode');
			$table->timestamps();

			$table->foreign('route_id')
			->references('id')
			->on('routes')
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
		Schema::drop('segments');
	}

}
