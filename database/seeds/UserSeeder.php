<?php

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder{
	/**
	 * Run the database seeders.
	 *
	 * @return void
	 */
	public function run(){
		$faker = Faker::create();
		$data  = [
			'username'   => $faker->userName(),
			'name'       => $faker->name(),
			'email'      => $faker->email(),
			'password'   => password_hash('password', PASSWORD_DEFAULT),
			'created_at' => (new Carbon())->subMinutes(61),
			'updated_at' => Carbon::now(),
		];
		Manager::connection()
			   ->table('users')
			   ->insert($data);
	}
}

