<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('spot_categories', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('main_cat');
            $table->string('main_cat_id');
            $table->string('sub_cat');
            $table->string('sub_cat_id');
            $table->string('icon_url')->nullable();
			$table->timestamps();

            $table->index('main_cat_id');
            $table->index('sub_cat_id');
		});
        Schema::table('spots', function (Blueprint $table) {
            $table->foreign('main_category_id')
                ->references('main_cat_id')
                ->on('spot_categories')
                ->onDelete('cascade');

            $table->foreign('sub_category_id')
                ->references('sub_cat_id')
                ->on('spot_categories')
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
		Schema::drop('spot_categories');
	}

}
