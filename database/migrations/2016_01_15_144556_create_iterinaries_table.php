<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIterinariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('iterinaries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('destination');
			$table->string('origin');
			$table->integer('pax')->unsigned();
			$table->integer('creator_id')->unsigned(); // reference original creator of iterinary
			$table->integer('route_id')->unsigned()->nullable();
			$table->timestamps();


			$table->foreign('creator_id')
			->references('id')
			->on('users')
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
		Schema::drop('iterinaries');
	}

}
