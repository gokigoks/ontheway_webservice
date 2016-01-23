<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('routes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('distance');
			$table->integer('duration');
			$table->integer('price');			
			$table->timestamps();
		});

		Schema::table('transports', function($table)
		{
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
		Schema::drop('routes');
	}

}
