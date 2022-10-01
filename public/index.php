<?php
declare(strict_types=1);

// Set the absolute path to the root directory.
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

$rootPath = realpath(dirname(__DIR__));
$srcPath  = $rootPath."/src";
$appPath  = $srcPath."/app";

// Autoloader
require $rootPath.'/vendor/autoload.php';

// Fire up DotEnv
$dotenv = Dotenv::createImmutable(__DIR__.'/..');
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
try{
	$container = $containerBuilder->build();
}
catch(Exception $ignored){
	exit('Can not start the application.');
}

// Instantiate the app
AppFactory::setContainer($container);
$app              = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Le container qui compose nos librairies
require $srcPath.'/config/container.php';

// Appel des middlewares
require $srcPath.'/config/middlewares.php';

// Le fichier ou l'on déclare les routes
require $srcPath.'/config/routes.php';

$container = $app->getContainer();
/**
 * @var SiteSettings
 */
$settings = $container->get(SiteSettings::class);

$displayErrors   = $settings->logger->displayErrorDetails;
$logErrors       = $settings->logger->logErrors;
$logErrorDetails = $settings->logger->logErrorDetails;
$logger          = $app->getContainer()
					   ->get(LoggerInterface::class);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request              = $serverRequestCreator->createServerRequestFromGlobals();

$request = new ServerRequest($request);

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler    = new HttpErrorHandler($callableResolver, $responseFactory);
// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrors, $logErrors, $logErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrors, $logErrors, $logErrorDetails, $logger);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response        = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
