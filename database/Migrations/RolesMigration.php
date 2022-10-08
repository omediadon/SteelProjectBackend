<?php

namespace Database\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RolesMigration extends Migration{
	const table = 'roles';
	public int $order = 1;

	public function up(){
		Capsule::schema()
			   ->create(self::table, function(Blueprint $table){
				   $table->increments('id');
				   $table->string('name')->unique();
				   $table->string('display_name');
				   $table->string('description')->nullable();
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
