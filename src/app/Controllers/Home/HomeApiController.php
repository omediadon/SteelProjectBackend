<?php

namespace App\Controllers\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Config\ApiInfo;
use System\Controllers\ApiController;

class HomeApiController extends ApiController{
	public function __construct(ContainerInterface $container){
		parent::__construct($container);
	}

	public function index(ServerRequest $request, Response $response) : Response{
		$this->request  = $request;
		$this->response = $response;
		$this->prepare();
		$this->data = new ApiInfo();

		return $this->render();
	}

	public function get404(ServerRequest $request, Response $response) : Response{
		$this->request  = $request;
		$this->response = $response;
		$this->prepare();

		return $this->render();
	}
}
