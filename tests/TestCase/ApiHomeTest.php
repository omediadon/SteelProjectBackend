<?php

namespace Tests\TestCase;

use PHPUnit\Framework\TestCase;
use SYSTEM\UTILS\StatusCodes;
use Tests\Traits\AppTestTrait;

class ApiHomeTest extends TestCase{
	use AppTestTrait;

	public function testAction() : void{

		$request  = $this->createRequest('GET', '/api');
		$response = $this->app->handle($request);

		$this->assertResponseContains($response, 'version');
		$this->assertSame(StatusCodes::HTTP_OK, $response->getStatusCode());
	}

	public function testPageNotFound() : void{
		$request  = $this->createRequest('GET', '/api/zefze');
		$response = $this->app->handle($request);

		$this->assertSame(StatusCodes::HTTP_NOT_FOUND, $response->getStatusCode());
	}
}