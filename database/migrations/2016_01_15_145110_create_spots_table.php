<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spots', function(Blueprint $table)
		{
			$table->increments('id');			
			$table->string('place_name');
			$table->string('pic_url')->nullable();
			$table->string('lat')->nullable();
			$table->string('lng')->nullable();
			$table->integer('price');
            $table->string('city')->nullable();
            $table->string('main_category_id');
            $table->string('sub_category_id')->nullable();
			$table->string('tips',200)->nullable();			
			$table->timestamps();

            $table->index('main_category_id');
            $table->index('sub_category_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('spots');
	}

}
