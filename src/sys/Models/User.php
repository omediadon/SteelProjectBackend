<?php

namespace System\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use System\Models\Enums\ActiveStatus;
use function password_hash;
use const PASSWORD_BCRYPT;

/**
 * @property int      id
 * @property string   name
 * @property string   username
 * @property string   email
 * @property string   password
 * @property string   status
 * @property string   profile_picture
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property DateTime deleted_at
 * @property string   remember_token
 */
class User extends Model{
	use SoftDeletes;
	use HasFactory;

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	protected $attributes = [
		'status' => ActiveStatus::STATUS_INACTIVE,
	];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'activate_token',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<string>
	 */
	protected $fillable = [
		'name',
		'username',
		'email',
		'password',
		'status',
		'activate_token',
		'profile_picture',
		'created_at',
		'updated_at',
		'deleted_at',
		'remember_token',
	];

	/**
	 * Always encrypt password when it is being updated.
	 *
	 * @param $value
	 *
	 */
	public function setPasswordAttribute($value){
		$this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
	}
}
