<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class UserMigration{
	function run(){
		Capsule::schema()
			   ->dropIfExists('users');
		Capsule::schema()
			   ->create('users', function(Blueprint $table){
				   $table->id();
				   $table->string('username')
						 ->unique();
				   $table->string('name')
						 ->nullable();
				   $table->string('password');
				   $table->string('email')
						 ->unique();
				   $table->timestamps();
			   });
	}
}