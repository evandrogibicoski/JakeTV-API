<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbl_category', function(Blueprint $table)
		{
			$table->integer('catid', true);
			$table->text('catidu', 65535);
			$table->string('category');
			$table->integer('selected')->default(0);
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
		Schema::drop('tbl_category');
	}

}
