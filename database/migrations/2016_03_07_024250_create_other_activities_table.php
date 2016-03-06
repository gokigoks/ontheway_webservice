<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('other_activities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('expense');
            $table->string('name');
            $table->string('review');
            $table->string('lng');
            $table->string('lat');
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
		Schema::drop('other_activities');
	}

}
