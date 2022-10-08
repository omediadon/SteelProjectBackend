<?php

namespace Database\Migrations;

use App\Models\Role;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use System\Models\Enums\ActiveStatus;

class UserMigration extends Migration{
	const table = 'users';
	public int $order = 4;

	public function up(){
		Capsule::schema()
			   ->create(self::table, function(Blueprint $table){
				   $table->id();
				   $table->string('username')
						 ->unique();
				   $table->string('name')
						 ->nullable();
				   $table->string('password');
				   $table->string('email')
						 ->unique();
				   $table->integer('role_id')->unsigned();
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
		Capsule::schema()
			   ->table(self::table, function(Blueprint $table){
			$table->foreign('role_id')
				  ->references('id')
				  ->on('roles');
		});
	}

	public function down(){
		Capsule::schema()
			   ->disableForeignKeyConstraints();
		Capsule::schema()
			   ->dropIfExists(self::table);
		Capsule::schema()
			   ->enableForeignKeyConstraints();
	}
}
