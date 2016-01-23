<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('origin');
			$table->string('destination');
			$table->integer('route_id')->unsigned()->nullable();
			$table->string('tips',200)->nullable();
			$table->timestamps();

			
		});

		Schema::table('iterinaries', function($table)
		{
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
		Schema::drop('transports');
	}

}
