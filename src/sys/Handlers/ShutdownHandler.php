<?php

namespace System\Handlers;

use Slim\Exception\HttpInternalServerErrorException;
use Slim\Http\ServerRequest;
use Slim\ResponseEmitter;

final class ShutdownHandler{
	private ServerRequest    $request;
	private HttpErrorHandler $errorHandler;
	private bool             $displayErrorDetails;
	private bool             $logErrors;
	private bool             $logErrorDetails;

	/**
	 * ShutdownHandler constructor.
	 *
	 * @param ServerRequest    $request
	 * @param HttpErrorHandler $errorHandler
	 * @param bool             $displayErrorDetails
	 * @param bool             $logErrors
	 * @param bool             $logErrorDetails
	 */
	public function __construct(ServerRequest $request, HttpErrorHandler $errorHandler, bool $displayErrorDetails,
								bool $logErrors, bool $logErrorDetails){
		$this->request             = $request;
		$this->errorHandler        = $errorHandler;
		$this->displayErrorDetails = $displayErrorDetails;
		$this->logErrors           = $logErrors;
		$this->logErrorDetails     = $logErrorDetails;
	}

	public function __invoke() : void{
		$error = error_get_last();
		if($error){
			$errorFile    = $error['file'];
			$errorLine    = $error['line'];
			$errorMessage = $error['message'];
			$errorType    = $error['type'];
			$message      = 'An error while processing your request. Please try again later.';

			if($this->displayErrorDetails){
				switch($errorType){
					case E_USER_ERROR:
						$message = "FATAL ERROR: {$errorMessage}. ";
						$message .= " on line {$errorLine} in file {$errorFile}.";
						break;

					case E_USER_WARNING:
						$message = "WARNING: {$errorMessage}";
						break;

					case E_USER_NOTICE:
						$message = "NOTICE: {$errorMessage}";
						break;

					default:
						$message = "ERROR: {$errorMessage}";
						$message .= " on line {$errorLine} in file {$errorFile}.";
						break;
				}
			}

			$exception = new HttpInternalServerErrorException($this->request, $message);
			$response  = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, $this->logErrors,
													   $this->logErrorDetails);

			$responseEmitter = new ResponseEmitter();
			$responseEmitter->emit($response);
		}
	}
}
