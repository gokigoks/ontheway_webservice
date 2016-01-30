<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainHopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('train_hops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('segment_id')->unsigned();
			$table->string('url')->nullable();
			$table->string('sName')->nullable();
			$table->string('tName')->nullable();
			$table->string('sPos')->nullable();
			$table->string('tPos')->nullabe();
			$table->string('duration')->nullable();
			$table->integer('price')->nullable();
			$table->integer('frequency')->nullable();
			$table->string('train_agency')->nullable();			
		

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
		Schema::drop('train_hops');
	}

}
