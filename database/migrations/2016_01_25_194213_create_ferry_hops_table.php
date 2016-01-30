<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFerryHopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ferry_hops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('segment_id')->unsigned();
			$table->string('url')->nullable();
			$table->string('sPos')->nullable();
			$table->string('tPos')->nullable();
			$table->string('sName')->nullable();
			$table->string('tName')->nullabe();
			$table->integer('frequency')->nullable();
			$table->integer('duration')->nullable();
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
		Schema::drop('ferry_hops');
	}

}
