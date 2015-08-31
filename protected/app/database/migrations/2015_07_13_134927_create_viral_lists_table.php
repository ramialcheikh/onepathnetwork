<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViralListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::create('viral_lists', function($table) {
            $table->engine = 'MyISAM';
            $table->timestamps();

            $table->increments('id');
            $table->string('title');
            $table->mediumText('description');
            $table->mediumText('content');
            $table->string('image');
            $table->string('creator_user_id');
            $table->integer('category_id')->unsigned;
            $table->integer('created_from_list_id');
            $table->boolean('original_creator_notified')->default(false);
            $table->integer('views');
            $table->enum('status', array('approved', 'awaiting_approval', 'disapproved', 'not_submitted'))->nullable();

            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
        });
        DB::statement('ALTER TABLE viral_lists ADD FULLTEXT search(title, description)');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
        Schema::table('viral_lists', function($table) {
            $table->drop('search');
        });
        Schema::drop('viral_lists');
	}

}
