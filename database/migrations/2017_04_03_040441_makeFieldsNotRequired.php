<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFieldsNotRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_user', function (Blueprint $table) {
          $schema = \DB::getDoctrineSchemaManager($table);
          $schema->getDatabasePlatform()->registerDoctrineTypeMapping('bit', 'boolean');

          $table->string('googleplusid')->nullable()->change();
          $table->string('password')->nullable()->change();
          $table->string('picture')->nullable()->change();
          $table->string('catid')->nullable()->change();
          $table->string('sessid')->nullable()->change();
          $table->date('cr_date')->default('1970-01-02')->change();
          $table->date('modify_date')->default('1970-01-02')->change();
          $table->integer('status')->default(1)->change();
          $table->boolean('subscribed')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
