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
		Schema::table('stop_transport', function(Blueprint $table)
		{
			$table->integer('stop_id');
			$table->integer('transport_id');

			$table->foreign('stop_id')
			->references('id')
			->on('stops')
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
