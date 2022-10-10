<?php

namespace App\Controllers\User;

use App\Models\Role;
use App\Models\User;
use App\Validation\JwtAuth;
use Illuminate\Database\Eloquent\Builder;
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
		$this->data  = [
			$user->can('can_edit_reviews'),
			$anotherUser->can('can_edit_reviews'),
		];

		return $this->render();
	}

	public function thesignup(ServerRequest $request, Response $response): Response{
		$passwordRegex = '/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/';
		$messages      = [
			'name.required'     => $this->translator->t('Username is required.'),
			'username.required' => $this->translator->t('Username is required.'),
			'password.required' => $this->translator->t('Password is required.'),
			'password.regex'    => $this->translator->t('Password is not strong enough.'),
			'email.required'    => $this->translator->t('Email is required.'),
			'email.email'       => $this->translator->t('Email format is incorrect.'),
		];
		$validation    = $this->validator->make($request->getParsedBody() ?? [], [
			'username' => 'required',
			'name'     => 'required',
			'password' => 'required|regex:' . $passwordRegex,
			'email'    => 'required|email',
		], $messages);
		$this->data    = [];
		if($validation->fails()){
			$this->status       = StatusCodes::HTTP_BAD_REQUEST;
			$this->data['fail'] = $validation->getMessageBag();
		}
		else{
			$this->performSignUp($this->request->getParsedBody());
		}

		return $this->render();
	}

	private function performSignUp(array $signupData){
		/**
		 * @var Builder $query
		 */
		$query = User::where(function(Builder $query) use ($signupData){
			$query->where('username', '=', $signupData['username'])
				  ->orWhere('email', '=', $signupData['email']);
		});
		$userExists = $query->count()>0;

		if(!$userExists){
			$user           = new User();
			$user->password = $signupData['password'];
			$user->username = $signupData['username'];
			$user->name     = $signupData['name'];
			$user->email    = $signupData['email'];
			$user->role()
				 ->associate(Role::where('name', 'member')
								 ->first());
			// $user->touch();
			if($user->save()){

				$this->data['success']['auth']['id']      = $user->id;
				$this->data['success']['auth']['message'] = $this->translator->t('User has been created successfully');

				return;
			}
		}

		$this->status               = StatusCodes::HTTP_UNAUTHORIZED;
		$this->data['fail']['auth'] = $this->translator->t('Could not sign up.');
	}

	public function thelogin(ServerRequest $request, Response $response): Response{
		$messages   = [
			'username.required' => $this->translator->t('Username is required.'),
			'password.required' => $this->translator->t('Email is required.'),
		];
		$validation = $this->validator->make($request->getParsedBody() ?? [], [
			'username' => 'required',
			'password' => 'required',
		], $messages);
		$this->data = [];
		if($validation->fails()){
			$this->status       = StatusCodes::HTTP_BAD_REQUEST;
			$this->data['fail'] = $validation->getMessageBag();
		}
		else{
			$this->performLogin($this->request->getParsedBody());
		}

		return $this->render();
	}

	private function performLogin($loginData){
		/**
		 * @var User $user
		 */
		$user = User::where('username', $loginData['username'])
					->first();
		if(isset($user)){
			$valid = password_verify($loginData['password'], $user->password);
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
