<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblAdminTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbl_admin', function(Blueprint $table)
		{
			$table->integer('adminid', true);
			$table->string('name');
			$table->string('email');
			$table->string('password');
			$table->integer('status');
			$table->dateTime('cr_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbl_admin');
	}

}
