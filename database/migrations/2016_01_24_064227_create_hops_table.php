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
			$table->integer('flight_iterinary_id')->unsigned();
			$table->timestamps();
			
			$table->foreign('flight_iterinary_id')
			->references('id')
			->on('flight_iterinaries')
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
