<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rating_user', function(Blueprint $table)
		{
			$table->increments('id');	
			$table->integer('user_id')->unsigned();
			$table->integer('rating_id')->unsigned();
			$table->timestamps();

			$table->foreign('user_id')
			->references('id')
			->on('users')
			->onDelete('cascade');

			$table->foreign('rating_id')
			->references('id')
			->on('ratings')
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
		Schema::table('rating_user', function(Blueprint $table)
		{
			//
		});
	}

}
