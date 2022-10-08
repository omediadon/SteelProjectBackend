<?php

namespace Database\Seeds;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder{
	protected array $roles = [
		'admin'     => [
			'display_name' => 'Administrator',
			'description'  => 'administer the website, basically God mode',
			'permissions'  => [
				'can_add_users',
				'can_edit_users',
				'can_add_comments',
				'can_edit_comments',
				'can_edit_own_comments',
				'can_add_reviews',
				'can_edit_reviews',
				'can_edit_own_reviews',
			],
		],
		'moderator' => [
			'display_name' => 'Moderator',
			'description'  => 'moderate all the content, keeping things clean',
			'permissions'  => [
				'can_edit_users',
				'can_add_comments',
				'can_edit_comments',
				'can_edit_own_comments',
				'can_add_reviews',
				'can_edit_reviews',
				'can_edit_own_reviews',
			],
		],
		'member'    => [
			'display_name' => 'Signed-in User',
			'description'  => 'basic user with no "special" abilities',
			'permissions'  => [
				'can_add_comments',
				'can_edit_own_comments',
			],
		],
		'reviewer'  => [
			'display_name' => 'Reviewer',
			'description'  => 'special user with some "extra" permissions',
			'permissions'  => [
				'can_add_comments',
				'can_edit_own_comments',
				'can_add_reviews',
				'can_edit_own_reviews',
			],
		],
	];

	protected array $permissions = [
		'can_add_users'         => [
			'display_name' => 'Add New Users',
			'description'  => 'Has the ability to add new users',
		],
		'can_edit_users'        => [
			'display_name' => 'Edit Users',
			'description'  => 'Has the ability to add new users',
		],
		'can_add_comments'      => [
			'display_name' => 'Add Comments',
			'description'  => 'ability to add new comments',
		],
		'can_edit_comments'     => [
			'display_name' => 'Edit Comments',
			'description'  => 'edit any comment',
		],
		'can_edit_own_comments' => [
			'display_name' => 'Edit Own Comments',
			'description'  => 'edit self made comment',
		],
		'can_add_reviews'       => [
			'display_name' => 'Add Feature Reviews',
			'description'  => 'ability to add new feature review',
		],
		'can_edit_reviews'      => [
			'display_name' => 'Edit Reviews',
			'description'  => 'ability to edit a feature review',
		],
		'can_edit_own_reviews'  => [
			'display_name' => 'Edit Own Reviews',
			'description'  => 'ability to edit only self-made reviews',
		],
	];

	public function run(){
		foreach($this->permissions as $key=>$permission){
			$permission['name']=$key;
			Capsule::table('permissions')
				   ->insert($permission);
		}
		foreach($this->roles as $key =>$role){
			$role['name'] = $key;
			$links = $role['permissions'];
			unset($role['permissions']);
			Capsule::table('roles')
				   ->insert($role);
			/**
			 * @var Role $role
			 */
			$role = Role::where('name', $key)->first();
			foreach($links as $link){
				$permission = Permission::where('name', $link)
										->first();
				$role->permissions()->attach($permission->id);
			}
		}
	}

}
