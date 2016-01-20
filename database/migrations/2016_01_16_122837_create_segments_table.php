<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('segments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('segment_origin');
			$table->string('segment_destination');
			$table->integer('price');
			$table->integer('distance');
			$table->integer('duration');
			$table->enum('mode', array('train','walk','ferry','plane','taxi'));
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
		Schema::drop('segments');
	}

}