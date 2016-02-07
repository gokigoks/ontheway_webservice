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
		/**
		 * NAA DRE ANG START DATE UG STATUS SA TRIP
		 * @param type 'iterinary_user' 
		 * @param function(Blueprint $table 
		 * @return void
		 */

		Schema::create('iterinary_user', function(Blueprint $table)
		{	
			$table->integer('user_id')->unsigned();
			$table->integer('iterinary_id')->unsigned();			
			$table->dateTime('date_start');
			$table->enum('status',array('planned','doing','done'))->default('planned');
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
