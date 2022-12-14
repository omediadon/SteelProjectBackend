<?php

namespace System\Handlers;

use Exception;
use ParseError;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use System\Utils\StatusCodes;

final class HttpErrorHandler extends SlimErrorHandler{
	/**
	 * @inheritdoc
	 */
	protected function respond() : ResponseInterface{
		$exception  = $this->exception;
		$statusCode = 500;
		$error      = new ActionError(ActionError::SERVER_ERROR, 'An internal error has occurred while processing your request.',
									  get_class($exception));

		if($exception instanceof HttpException){
			$statusCode = $exception->getCode();
			$error->setDescription($exception->getMessage());

			if($exception instanceof HttpNotFoundException){
				$error->setType(ActionError::RESOURCE_NOT_FOUND);
			}
			elseif($exception instanceof HttpMethodNotAllowedException){
				$error->setType(ActionError::NOT_ALLOWED);
			}
			elseif($exception instanceof HttpUnauthorizedException){
				$error->setType(ActionError::UNAUTHENTICATED);
			}
			elseif($exception instanceof HttpForbiddenException){
				$error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
			}
			elseif($exception instanceof HttpBadRequestException){
				$error->setType(ActionError::BAD_REQUEST);
			}
			elseif($exception instanceof HttpNotImplementedException){
				$error->setType(ActionError::NOT_IMPLEMENTED);
			}
		}

		if($exception instanceof HttpInternalServerErrorException && $this->displayErrorDetails){
			$error->setDescription($exception->getMessage());
		}

		if(!($exception instanceof HttpException) && $exception instanceof Exception && $this->displayErrorDetails){
			$error->setDescription($exception->getMessage());
		}

		if($exception instanceof ParseError){
			$des = "FATAL ERROR: ".$exception->getMessage();
			$des .= " on line ".$exception->getLine()." in file ".$exception->getFile();
			$error->setDescription($des);
		}

		$payload = [
			"error" => [
				"code"        => $statusCode,
				"message"     => StatusCodes::getMessageForCode($statusCode),
				"description" => $error->getDescription(),
			],
		];
		//$payload ["error"]["type"] = $error->getClass();
		//$payload        = new ActionPayload($statusCode, null, $error);
		$encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

		$response = $this->responseFactory->createResponse($statusCode);
		if($this->request->getHeader("Accept") == "application/json"){
			$response->getBody()
					 ->write($encodedPayload);
			$response->withHeader('Content-Type', 'application/json');
		}
		else{
			$renderer = $this->determineRenderer();
			$body     = call_user_func($renderer, $this->exception, $this->displayErrorDetails);
			$response->getBody()
					 ->write($body);
		}

		return $response;
	}
}
