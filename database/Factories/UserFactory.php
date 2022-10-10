<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use System\Faker\Provider\ImageProvider;
use System\Models\Enums\ActiveStatus;
use System\Utils\FileUtils;
use function file_exists;
use function mkdir;

class UserFactory extends Factory{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = User::class;

	public function definition(){
		return [
			'username' => $this->faker->userName(),
			'name'     => $this->faker->name(),
			'email'    => $this->faker->email(),
			'role_id'  => function(){
				return Role::where('name', 'member')
						   ->first()->id;
			},
			'password' => password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]),
			'status'   => ActiveStatus::STATUS_INACTIVE,
		];
	}

	public function configure(): UserFactory{
		return $this->afterCreating(function(User $user){
			if($user->id == 1){
				$user->role()
					 ->associate(Role::where('name', 'admin')
									 ->first());
			}
			$dir   = FileUtils::avatarsPath();
			$dbDir = FileUtils::avatarsPublicPath();
			if(!file_exists($dir)){
				mkdir($dir, recursive: true);
			}
			$path                  = $this->faker->image($dir, 640, 640, null, false);
			$user->profile_picture = $dbDir . DIRECTORY_SEPARATOR . $path;
			$user->save();
		});
	}

	protected function withFaker(): Generator{
		$faker = Faker::create();
		$faker->addProvider(new ImageProvider($faker));

		return $faker;
	}
}
