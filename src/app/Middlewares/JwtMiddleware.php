<?php

namespace App\Middlewares;

use App\Validation\JwtAuth;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;
use System\Utils\StatusCodes;

final class JwtMiddleware implements MiddlewareInterface{
	public function __construct(private JwtAuth $jwtAuth, private ResponseFactoryInterface $responseFactory){
	}

	/**
	 * Invoke middleware.
	 *
	 * @param ServerRequestInterface  $request The request
	 * @param RequestHandlerInterface $handler The handler
	 *
	 * @return ResponseInterface The response
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface{
		$authorization = explode(' ', $request->getHeaderLine('Authorization'));
		$token         = $authorization[1] ?? '';

		if(!$token || !$this->jwtAuth->validateToken($token)){
			$payload = [
				"error" => [
					"code"    => StatusCodes::HTTP_UNAUTHORIZED,
					"message" => StatusCodes::getMessageForCode(StatusCodes::HTTP_UNAUTHORIZED),
				],
			];
			$stream  = (new StreamFactory())->createStream(json_encode($payload, JSON_PRETTY_PRINT));

			return $this->responseFactory->createResponse()
										 ->withBody($stream)
										 ->withHeader('Content-Type', 'application/json')
										 ->withStatus(StatusCodes::HTTP_UNAUTHORIZED,
													  StatusCodes::getMessageForCode(StatusCodes::HTTP_UNAUTHORIZED));
		}

		return $handler->handle($request);
	}
}
