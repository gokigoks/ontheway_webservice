<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodCategoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('main_cat');
            $table->string('main_cat_id');
            $table->string('sub_cat');
            $table->string('sub_cat_id');
            $table->string('icon_url')->nullable();
            $table->timestamps();
        });

        Schema::table('eats', function (Blueprint $table) {
            $table->foreign('main_category_id')
                ->references('main_cat_id')
                ->on('food_categories')
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
		Schema::drop('food_categories');
	}

}
