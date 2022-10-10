<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use System\Models\Model;

/**
 * @property int          $id
 * @property string       $name
 * @property string       display_name
 * @property string       $description
 * @property Permission[] $permissions
 */
class Role extends Model{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'pivot',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'display_name',
		'description',
	];

	protected $with = ['permissions'];

	public function can(string $permission): bool{
		/**
		 * @var $permissions Permission[]
		 */
		$permissions = empty($this->permissions) ? $this->permissions()
														->get() : $this->permissions;
		foreach($permissions as $permissionDB){
			if($permissionDB->name == $permission){
				return true;
			}
		}

		return false;
	}

	public function permissions(): BelongsToMany{
		return $this->belongsToMany(Permission::class);
	}

	public function users(): HasMany{
		return $this->hasMany(User::class);
	}
}
