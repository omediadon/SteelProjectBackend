<?php

namespace Database\Seeds;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use System\Models\Enums\ActiveStatus;

class UserSeeder extends Seeder{
	/**
	 * Run the database seeders.
	 *
	 * @return void
	 */
	public function run(){
		// $faker = Faker::create();
		// $data  = [
		// 	'username'   => $faker->userName(),
		// 	'name'       => $faker->name(),
		// 	'email'      => $faker->email(),
		// 	'password'   => password_hash('password', PASSWORD_DEFAULT),
		// 	'created_at' => (new Carbon())->subMinutes(61),
		// 	'updated_at' => Carbon::now(),
		// ];
		// Manager::connection()
		// 	   ->table('users')
		// 	   ->insert($data);
		$activeSeq = new Sequence(['status' => ActiveStatus::STATUS_ACTIVE], [
			'status' => ActiveStatus::STATUS_INACTIVE,
		]);
		User::factory()->times(5)
						   ->state($activeSeq)
						   ->create();
	}
}
