<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblPostTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tbl_post', function(Blueprint $table)
		{
			$table->integer('postid', true);
			$table->string('title');
			$table->text('catid', 65535);
			$table->integer('subcatid');
			$table->text('image', 65535);
			$table->text('url', 65535);
			$table->string('kicker');
			$table->string('source');
			$table->text('description', 65535);
			$table->text('isbookmarked', 65535);
			$table->text('isliked', 65535);
			$table->string('totalpostlikes');
			$table->integer('status');
			$table->dateTime('publish')->nullable();
			$table->dateTime('cr_date');
			$table->dateTime('modify_date');
			$table->integer('sort_order');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tbl_post');
	}

}
