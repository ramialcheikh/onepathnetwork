<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViralListChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('viral_list_changes', function($table) {
            $table->timestamps();

            //ID of the list
            $table->integer('id');

            //The date for the list
            $table->longText('content');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('viral_list_changes');
	}

}
