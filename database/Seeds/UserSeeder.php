<?php

namespace Database\Seeds;

use App\Models\User;
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
		$activeSeq = new Sequence(['status' => ActiveStatus::STATUS_ACTIVE], [
			'status' => ActiveStatus::STATUS_INACTIVE,
		]);
		User::factory()
			->times(5)
			->state($activeSeq)
			->create();
	}
}
