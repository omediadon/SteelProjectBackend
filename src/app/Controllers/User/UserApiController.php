<?php

namespace App\Controllers\User;

use App\Models\Role;
use App\Models\User;
use App\Validation\JwtAuth;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Controllers\ApiController;
use System\Utils\StatusCodes;
use function password_verify;

class UserApiController extends ApiController{

	public function index(ServerRequest $request, Response $response): Response{
		$this->prepare($request, $response);
		/**
		 * @var Role $role
		 */
		// This is an admin
		$user = User::find(1);
		// This s a member
		$anotherUser = User::find(5);
		$data        = [
			$user->can('can_edit_reviews'),
			$anotherUser->can('can_edit_reviews'),
		];

		return $this->render($data);
	}

	public function login(ServerRequest $request, Response $response): Response{
		$this->prepare($request, $response);

		$val        = $this->validator;
		$messages   = [
			'username.required' => 'Username is required.',
			'password.required' => 'Email is required.',
		];
		$validation = $val->make($request->getParsedBody() ?? [], [
			'username' => 'required',
			'password' => 'required',
		], $messages);
		$this->data = [];
		if($validation->fails()){
			$this->status       = StatusCodes::HTTP_BAD_REQUEST;
			$this->data['fail'] = $validation->getMessageBag();
		}
		else{
			$this->performLogin();
		}

		return $this->render();
	}

	private function performLogin(){
		/**
		 * @var User $user
		 */
		$user = User::where('username', $this->request->getParsedBody()['username'])
					->first();
		if(isset($user)){
			$valid = password_verify($this->request->getParsedBody()['password'], $user->password);
			if($valid){
				/**
				 * @var JwtAuth $jwt
				 */
				$jwt                                      = $this->container->get(JwtAuth::class);
				$this->data['success']['auth']['token']   = $jwt->createJwt(['user' => $user->id]);
				$this->data['success']['auth']['refresh'] = $jwt->createJwt(['foruser' => $user->id], true);

				return;
			}
		}

		$this->status               = StatusCodes::HTTP_UNAUTHORIZED;
		$this->data['fail']['auth'] = $this->translator->t('Could not authenticate this user.');
	}
}
