<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stops', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('place_name');
			$table->string('kind');
			$table->string('lng');
			$table->string('lat');
			$table->string('details',200)->nullable();
            $table->integer('price');
        
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
		Schema::drop('stops');
	}

}
