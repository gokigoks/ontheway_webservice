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
			$table->integer('route_id')->unsigned();
            $table->integer('day')->default(1);
			$table->integer('sequence')->unsigned();
			$table->string('origin_name');
			$table->string('destination_name');
			$table->string('origin_pos');
			$table->string('destination_pos');
			$table->integer('price');
			$table->string('path')->nullable();
			$table->integer('distance');
			$table->integer('duration');
			$table->string('mode');
			$table->timestamps();
			
			$table->unique(array('route_id','sequence'));

			$table->foreign('route_id')
			->references('id')
			->on('routes')
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
		Schema::drop('segments');
	}	
		
}
