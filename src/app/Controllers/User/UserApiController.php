<?php

namespace App\Controllers\User;

use \Exception;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Controllers\ApiController;
use System\Models\User;

class UserApiController extends ApiController{

	public function index(ServerRequest $request, Response $response) : Response{
		$this->request  = $request;
		$this->response = $response;

		$users      = User::all();
		$this->data = $users;

		$this->prepare();

		return $this->render();
	}

}