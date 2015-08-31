<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    public function up()
    {
        Schema::create('users', function($table)
        {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('photo');
            $table->string('name');
            $table->string('password');
			$table->string('remember_token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('users');
    }

}