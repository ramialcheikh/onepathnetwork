<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCategoriesAddSeoTags extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::table('categories', function($table) {
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
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
        Schema::table('categories', function($table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });
	}

}
