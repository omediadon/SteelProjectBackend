<?php

namespace App\Controllers\Home;

use App\Validation\JwtAuth;
use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Config\ApiInfo;
use System\Controllers\ApiController;

class HomeApiController extends ApiController{

	public function index(ServerRequest $request, Response $response) : Response{
		$this->prepare($request, $response);

		$this->data = new ApiInfo();

		return $this->render();
	}

	public function getTestingToken(ServerRequest $request, Response $response) : Response{
		$this->prepare($request, $response);

		/**
		 * @var JwtAuth $jwt
		 */
		$jwt = $this->container->get(JwtAuth::class);
		$this->data = ['token'=>$jwt->createJwt(['ezerzerze'=>'dfgdfgdfg'])];

		return $this->render();
	}

	public function protectedRoute(ServerRequest $request, Response $response) : Response{
		$this->prepare($request, $response);

		$this->data = ['reult'=>'success'];

		return $this->render();
	}

	public function get404(ServerRequest $request, Response $response) : Response{
		$this->prepare($request, $response);

		return $this->render();
	}
}
