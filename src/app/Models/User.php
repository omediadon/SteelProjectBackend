<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\InvalidArgumentException;
use System\Models\User as SystemUser;

/**
 * @property Role $role
 */
class User extends SystemUser{
	protected $with = ['role'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'activate_token',
		'role_id',
		'role',
	];

	public function can(string $permission): bool{
		$role = empty($this->role) ? $this->role()
										  ->get() : $this->role;

		return $role->can($permission);
	}

	public function role(): BelongsTo{
		return $this->belongsTo(Role::class);
	}

}
