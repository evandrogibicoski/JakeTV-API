<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Fixsubcatid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_post', function (Blueprint $table) {
          $table->string('subcatid')->nullable()->change();
          $table->string('isbookmarked')->nullable()->change();
          $table->string('isliked')->nullable()->change();
          $table->string('totalpostlikes')->nullable()->change();
          $table->string('status')->default(2)->change();
          $table->datetime('cr_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
          $table->datetime('modify_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
          $table->datetime('publish')->nullable()->change();
          $table->integer('sort_order')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
