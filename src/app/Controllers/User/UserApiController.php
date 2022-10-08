<?php

namespace App\Controllers\User;

use App\Models\Role;
use App\Models\User;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Controllers\ApiController;

class UserApiController extends ApiController{

	public function index(ServerRequest $request, Response $response): Response{
		/**
		 * @var Role $role
		 */
		$this->request  = $request;
		$this->response = $response;
		// This is an admin
		$user           = User::find(1);
		// This s a member
		$anotherUser    = User::find(5);
		$this->data     = [
			$user->can('can_edit_reviews'),
			$anotherUser->can('can_edit_reviews'),
		];
		$this->prepare();

		return $this->render();
	}

}
