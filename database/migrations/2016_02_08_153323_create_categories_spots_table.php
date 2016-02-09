<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesSpotsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories_spots', function(Blueprint $table)
		{
			$table->integer('category_id')->unsigned();
			$table->integer('spots_id')->unsigned();

			$table->foreign('category_id')
			->references('id')
			->on('categories')
			->onDelete('cascade');

			$table->foreign('spots_id')
			->references('id')
			->on('spots')
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
		Schema::drop('categories_spots');		
	}

}
