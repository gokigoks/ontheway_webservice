<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentStopTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('segment_stop', function(Blueprint $table)
		{
			$table->integer('segment_id')->unsigned();
			$table->integer('stop_id')->unsigned();
			
			$table->foreign('segment_id')
			->references('id')
			->on('segments')
			->onDelete('cascade');

			$table->foreign('stop_id')
			->references('id')
			->on('stops')
			->onDelete('cascade');
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
		Schema::drop('segment_stop');
	}

}
