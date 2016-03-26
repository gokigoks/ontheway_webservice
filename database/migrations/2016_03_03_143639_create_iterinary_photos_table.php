<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIterinaryPhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('iterinary_photos', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('iterinary_id')->unsigned();
            $table->string('image_path');
			$table->timestamps();

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
		Schema::drop('iterinary_photos');
	}

}
