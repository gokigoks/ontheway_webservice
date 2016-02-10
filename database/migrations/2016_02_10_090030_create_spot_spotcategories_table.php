<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotSpotcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spot_spotcategory', function(Blueprint $table)
		{
            $table->integer('spotcategory_id')->unsigned();
            $table->integer('spots_id')->unsigned();

            $table->foreign('spotcategory_id')
                ->references('id')
                ->on('spot_categories')
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
        Schema::drop('spot_foodcategory');
	}

}
