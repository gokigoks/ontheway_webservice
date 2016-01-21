<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityTransportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_transport', function(Blueprint $table)
		{
			$table->integer('activity_id')->unsigned();
			$table->integer('transport_id')->unsigned();
			
			$table->foreign('activity_id')
			->references('id')
			->on('activities')
			->onDelete('cascade');

			$table->foreign('transport_id')
			->references('id')
			->on('transports')
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
		Schema::drop('activity_transport');
	}

}
