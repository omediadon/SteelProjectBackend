<?php

namespace Database\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PermissionsRolesMigration extends Migration{
	const table = 'permission_role';
	public int $order = 3;

	public function up(){
		Capsule::schema()
			   ->create(self::table, function(Blueprint $table){
				   $table->increments('id');
				   $table->integer('permission_id')
						 ->unsigned();
				   $table->integer('role_id')
						 ->unsigned();
				   $table->foreign('permission_id')
						 ->references('id')
						 ->on('permissions')
						 ->onDelete('cascade');
				   $table->foreign('role_id')
						 ->references('id')
						 ->on('roles')
						 ->onDelete('cascade');
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
