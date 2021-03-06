<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('segment_id')->unsigned();
			$table->string('source_area_code')->nullable();
			$table->string('url')->nullable();
			$table->string('target_area_code')->nullable();
			$table->string('source_terminal')->nullable();
			$table->string('target_terminal')->nullabe();
			$table->string('sTime');
			$table->string('tTime');
			$table->string('flight_no')->nullable();
			$table->string('airline_code')->nullable();
			$table->integer('duration');

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
		Schema::drop('hops');
	}

}
