<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use System\Models\User as SystemUser;

/**
 * @property Role $role
 */
class User extends SystemUser{
	protected $with = ['role'];

	public function can(string $permission): bool{
		$role = empty($this->role) ? $this->role()
										  ->get() : $this->role;

		return $role->can($permission);
	}

	public function role(): BelongsTo{
		return $this->belongsTo(Role::class);
	}

}
