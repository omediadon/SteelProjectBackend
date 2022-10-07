<?php

namespace Database\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use System\Models\Enums\ActiveStatus;

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
				   $table->string('profile_picture')
						 ->nullable();
				   $table->enum('status', [
					   ActiveStatus::STATUS_INACTIVE,
					   ActiveStatus::STATUS_ACTIVE,
				   ])
						 ->default(ActiveStatus::STATUS_INACTIVE);
				   $table->string('activate_token')
						 ->nullable();
				   $table->rememberToken();
				   $table->timestampsTz();
				   $table->softDeletesTz();
			   });
	}
}
