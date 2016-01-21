<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')
			$table->string('kind');
			$table->string('city');
			$table->string('pos');
			$table->string('tips',200)->nullable();
			$table->string('timezone');
			$table->string('region_code');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stops');
	}

}
