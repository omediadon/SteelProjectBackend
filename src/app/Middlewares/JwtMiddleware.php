<?php

namespace App\Middlewares;

use App\Validation\JwtAuth;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

final class JwtMiddleware implements MiddlewareInterface{
	/**
	 * @var JwtAuth
	 */
	private JwtAuth $jwtAuth;

	/**
	 * @var ResponseFactoryInterface
	 */
	private ResponseFactoryInterface $responseFactory;

	public function __construct(JwtAuth $jwtAuth, ResponseFactoryInterface $responseFactory){
		$this->jwtAuth         = $jwtAuth;
		$this->responseFactory = $responseFactory;
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
		$authorization = explode(' ', (string)$request->getHeaderLine('Authorization'));
		$token         = $authorization[1] ?? '';

		if(!$token || !$this->jwtAuth->validateToken($token)){
			$stream = (new StreamFactory())->createStream(json_encode([
																		  "error" => [
																			  "code"    => 401,
																			  "message" => "You are you doing here? This is a restricted are!",
																		  ],
																	  ], JSON_PRETTY_PRINT));

			return $this->responseFactory->createResponse()
										 ->withBody($stream)
										 ->withHeader('Content-Type', 'application/json')
										 ->withStatus(401, 'Unauthorized');
		}

		// Append valid token
		$parsedToken = $this->jwtAuth->createParsedToken($token);
		$request     = $request->withAttribute('authToken', $parsedToken);

		// Append the user id as request attribute
		$request = $request->withAttribute('authContext', $parsedToken->getClaim('context'));

		return $handler->handle($request);
	}
}
