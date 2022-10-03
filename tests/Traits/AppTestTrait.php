<?php

namespace Tests\Traits;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\TestTrait\Traits\ArrayTestTrait;
use Selective\TestTrait\Traits\ContainerTestTrait;
use Selective\TestTrait\Traits\HttpJsonTestTrait;
use Selective\TestTrait\Traits\HttpTestTrait;
use Selective\TestTrait\Traits\MockTestTrait;
use Slim\App;
use Slim\Http\ServerRequest;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

trait AppTestTrait{
	use ArrayTestTrait;
	use ContainerTestTrait;
	use HttpTestTrait;
	use HttpJsonTestTrait;
	use MockTestTrait;

	protected App $app;

	/**
	 * Before each test.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function setUp() : void{
		$appFactory = new \System\App();
		$this->app  = $appFactory->getApp();

		$container = $this->app->getContainer();

		$this->setUpContainer($container);
	}

	protected function createRequest(string $method, string $path, array $headers = ['HTTP_ACCEPT' => 'application/json'],
									 array $cookies = [], array $serverParams = []) : ServerRequest{
		$uri    = new Uri('', '', 80, $path);
		$handle = fopen('php://temp', 'w+');
		$stream = (new StreamFactory())->createStreamFromResource($handle);

		$h = new Headers();
		foreach($headers as $name => $value){
			$h->addHeader($name, $value);
		}

		$req = new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);

		return new ServerRequest($req);
	}
}