<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIterinariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('iterinaries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('destination');
			$table->string('origin');
			$table->integer('pax');			
			$table->integer('transport_id')->unsigned()->nullable();
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
		Schema::drop('iterinaries');
	}

}
