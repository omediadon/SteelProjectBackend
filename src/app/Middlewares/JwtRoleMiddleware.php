<?php

namespace App\Middlewares;

use App\Models\User;
use App\Validation\JwtAuth;
use DI\Container;
use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;
use System\Utils\StatusCodes;
use function explode;
use function json_encode;
use function property_exists;
use const JSON_PRETTY_PRINT;

class JwtRoleMiddleware implements MiddlewareInterface{
	private ResponseFactoryInterface $responseFactory;
	private Manager                  $manager;
	private JwtAuth                  $jwtAuth;

	public function __construct(Container $container, private string $role){
		$this->jwtAuth         = $container->get(JwtAuth::class);
		$this->manager         = $container->get(Manager::class);
		$this->responseFactory = $container->get(ResponseFactoryInterface::class);
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
		$authorization = explode(' ', $request->getHeaderLine('Authorization'));
		$token         = $authorization[1] ?? '';

		if($token || $this->jwtAuth->validateToken($token)){
			$context = $this->jwtAuth->getContextFromToken($token);
			if(property_exists($context, "user")){
				/**
				 * @var User $user
				 */
				$user          = User::find($context->user);
				$isCorrectRole = $user->role->name == $this->role;

				if($isCorrectRole){
					return $handler->handle($request);
				}
			}
		}
		$payload = [
			'error' => [
				'code'    => StatusCodes::HTTP_UNAUTHORIZED,
				'message' => StatusCodes::getMessageForCode(StatusCodes::HTTP_UNAUTHORIZED),
			],
		];
		$stream  = (new StreamFactory())->createStream(json_encode($payload, JSON_PRETTY_PRINT));

		return $this->responseFactory->createResponse()
									 ->withBody($stream)
									 ->withHeader('Content-Type', 'application/json')
									 ->withStatus(StatusCodes::HTTP_UNAUTHORIZED,
												  StatusCodes::getMessageForCode(StatusCodes::HTTP_UNAUTHORIZED));
	}
}
