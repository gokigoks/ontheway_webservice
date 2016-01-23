<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('days', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('iterinary_id')->unsigned();			
			$table->integer('day_no')->unsigned();
			$table->timestamps();

			$table->unique(array('iterinary_id','day_no'));

			$table->foreign('iterinary_id')
			->references('id')
			->on('iterinaries')
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
		Schema::drop('days');
	}

}
