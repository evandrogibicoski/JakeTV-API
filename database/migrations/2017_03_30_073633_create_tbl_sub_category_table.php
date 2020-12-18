<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblSubCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbl_sub_category', function(Blueprint $table)
		{
			$table->integer('subcatid', true);
			$table->integer('subcatidu');
			$table->integer('catidu');
			$table->string('subcategory');
			$table->integer('selected');
			$table->integer('status');
			$table->dateTime('cr_date');
			$table->dateTime('modify_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbl_sub_category');
	}

}
