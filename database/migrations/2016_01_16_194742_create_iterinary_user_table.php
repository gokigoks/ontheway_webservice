<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIterinaryUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::create('iterinary_user', function(Blueprint $table)
		{	
			$table->integer('user_id')->unsigned();
			$table->integer('iterinary_id')->unsigned();
			$table->dateTime('start_date');
			$table->enum('status',array('planned','doing','done'));
			$table->timestamps();

			$table->foreign('user_id')
			->references('id')
			->on('users')
			->onDelete('cascade');

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
		Schema::table('iterinary_user', function(Blueprint $table)
		{
			//
		});
	}

}
