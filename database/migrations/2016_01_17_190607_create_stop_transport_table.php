<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopTransportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stop_transport', function(Blueprint $table)
		{
			$table->integer('stop_id')->unsigned();
			$table->integer('transport_id')->unsigned();

			$table->foreign('stop_id')
			->references('id')
			->on('stops')
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
		Schema::table('stop_transport', function(Blueprint $table)
		{
			//
		});
	}

}
