<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusHopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bus_hops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('segment_id')->unsigned();
			$table->string('sName')->nullable();
			$table->string('tName')->nullable();
			$table->string('sPos')->nullable();
			$table->string('tPos')->nullabe();
			$table->integer('frequency')->nullable();
			$table->integer('duration');
			$table->integer('price');
			$table->string('agency')->nullable();
		

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
		Schema::drop('bus_hops');
	}

}
