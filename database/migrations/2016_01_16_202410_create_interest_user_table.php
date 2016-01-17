<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterestUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interest_user', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned();
			$table->integer('interest_id')->unsigned();

			$table->foreign('user_id')
			->references('id')
			->on('users')
			->onDelete('cascade');

			$table->foreign('interest_id')
			->references('id')
			->on('interests')
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
		Schema::table('interest_user', function(Blueprint $table)
		{
			//
		});
	}

}
