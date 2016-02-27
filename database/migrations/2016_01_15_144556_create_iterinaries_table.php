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
			$table->integer('pax')->unsigned()->default(1);
            $table->integer('days')->unsigned()->default(1);
            $table->integer('distance')->default(0);
            $table->integer('duration')->default(0);
            $table->integer('price')->default(0);
			$table->integer('creator_id')->unsigned();
			$table->integer('route_id')->unsigned()->nullable();
			$table->timestamps();



			$table->foreign('creator_id')
			->references('id')
			->on('users')
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
		Schema::drop('iterinaries');
	}

}
