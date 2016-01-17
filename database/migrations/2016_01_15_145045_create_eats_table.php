<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('place_name');
			$table->integer('price');
			$table->string('tips',200)->nullable();
			$table->integer('long')->nullable();
			$table->integer('lat')->nullable();
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
		Schema::drop('eats');
	}

}
