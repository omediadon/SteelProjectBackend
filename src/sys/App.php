<?php

namespace System;

use Config\ContainerFactory;
use Config\MiddlewareFactory;
use Config\RoutesFactory;
use DI\Container;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Http\ServerRequest;
use Slim\ResponseEmitter;
use System\Config\SiteSettings;
use System\Handlers\HttpErrorHandler;
use System\Handlers\ShutdownHandler;

class App{
	private Container $container;
	private \Slim\App $app;
	private           $request;

	public function __construct(){
		if(!defined("PARENT_DIR")){
			define("PARENT_DIR", DIRECTORY_SEPARATOR . '..');
		}
		$rootPath = realpath(dirname(__DIR__ . PARENT_DIR . PARENT_DIR . PARENT_DIR));
		$srcPath  = $rootPath . DIRECTORY_SEPARATOR . "src";
		$appPath  = $srcPath . DIRECTORY_SEPARATOR . "app";

		// Fire up DotEnv
		$dotenv = Dotenv::createImmutable($rootPath);
		$dotenv->load();
		$dotenv->required([
							  'ENVIRONMENT',
							  'DB_DRIVER',
							  'DB_HOST',
							  'DB_NAME',
							  'DB_USER',
							  'DB_PASS',
							  'DB_PREFIX',
						  ]);

		// Show all php error when in debug or dev mode
		if($_ENV['ENVIRONMENT'] == 'DEBUG' || $_ENV['ENVIRONMENT'] == 'DEV'){
			ini_set('display_errors', 'On');
			ini_set('display_startup_errors', 'On');
			error_reporting(E_ALL);
		}

		// Instantiate PHP-DI ContainerBuilder
		$containerBuilder = new ContainerBuilder();

		// Build PHP-DI Container instance
		$this->container = $containerBuilder->build();

		// Instantiate the app
		AppFactory::setContainer($this->container);
		$this->app        = AppFactory::create();
		$callableResolver = $this->app->getCallableResolver();

		// Set up the container
		ContainerFactory::setUpContainer($this->container);

		// Set up the middlewares
		MiddleWareFactory::setUpMiddlewares($this->app);

		// Set up the routes
		RoutesFactory::setUpRoutes($this->app);

		$container = $this->app->getContainer();
		/**
		 * @var SiteSettings
		 */
		$settings = $container->get(SiteSettings::class);

		$displayErrors   = $settings->logger->displayErrorDetails;
		$logErrors       = $settings->logger->logErrors;
		$logErrorDetails = $settings->logger->logErrorDetails;
		$logger          = $this->app->getContainer()
									 ->get(LoggerInterface::class);

		// Create Request object from globals
		$serverRequestCreator = ServerRequestCreatorFactory::create();
		$request              = $serverRequestCreator->createServerRequestFromGlobals();

		$this->request = new ServerRequest($request);

		// Create Error Handler
		$responseFactory = $this->app->getResponseFactory();
		$errorHandler    = new HttpErrorHandler($callableResolver, $responseFactory);
		// Create Shutdown Handler
		$shutdownHandler = new ShutdownHandler($this->request, $errorHandler, $displayErrors, $logErrors, $logErrorDetails);
		register_shutdown_function($shutdownHandler);

		// Add Routing Middleware
		$this->app->addRoutingMiddleware();

		// Add Error Middleware
		$errorMiddleware = $this->app->addErrorMiddleware($displayErrors, $logErrors, $logErrorDetails, $logger);
		$errorMiddleware->setDefaultErrorHandler($errorHandler);
	}

	public function emit(){
		// Run App & Emit Response
		$response        = $this->app->handle($this->request);
		$responseEmitter = new ResponseEmitter();
		$responseEmitter->emit($response);
	}

	/**
	 * @return \Slim\App
	 */
	public function getApp() : \Slim\App{
		return $this->app;
	}

	/**
	 * @return Container
	 */
	public function getContainer() : Container{
		return $this->container;
	}

}