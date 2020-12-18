<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbl_user', function(Blueprint $table)
		{
			$table->integer('userid', true);
			$table->string('googleplusid');
			$table->string('fname');
			$table->string('lname');
			$table->string('email');
			$table->string('password');
			$table->text('picture', 65535);
			$table->text('catid', 65535);
			$table->integer('status');
			$table->dateTime('cr_date');
			$table->dateTime('modify_date');
			$table->text('sessid', 65535);
			$table->boolean('subscribed', 1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbl_user');
	}

}
