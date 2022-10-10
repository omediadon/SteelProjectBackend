<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use System\Models\Model;

/**
 * @property int    id
 * @property string name
 * @property string display_name
 * @property string description
 */
class Permission extends Model{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

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

	public function roles(): BelongsToMany{
		return $this->belongsToMany(Role::class);
	}
}
