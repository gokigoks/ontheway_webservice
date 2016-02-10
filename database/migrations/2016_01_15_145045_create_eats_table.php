<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eats', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('place_name');
			$table->integer('price');
			$table->string('pic_url')->nullable();
			$table->string('tips',200)->nullable();
			$table->string('pos')->nullable();
            $table->string('main_category_id');
            $table->string('sub_category_id')->nullable();
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
		Schema::drop('eats');
	}

}
