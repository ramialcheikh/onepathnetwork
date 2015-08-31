<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddConfirmation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::table('users', function($table) {
            $table->boolean('confirmed')->default(0);
            $table->string('confirmation_code')->nullable();
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
        Schema::table('users', function($table) {
            $table->dropColumn(['confirmed', 'confirmation_code']);
        });
	}

}
