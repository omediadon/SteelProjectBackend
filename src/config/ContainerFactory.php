<?php

namespace Config;

use App\Validation\JwtAuth;
use Illuminate\Database\Capsule\Manager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Selective\Validation\Encoder\JsonEncoder;
use Selective\Validation\Middleware\ValidationExceptionMiddleware;
use Selective\Validation\Transformer\ErrorDetailsResultTransformer;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteParser;
use Slim\Views\Twig;
use SlimSession\Helper;
use System\Config\SiteSettings;
use System\Utils\Translator;

class ContainerFactory{
	private function __construct(){
	}

	static function setUpContainer(&$container){

		$container->set(SiteSettings::class, function(){
			return new SiteSettings();
		});

		$container->set(App::class, static function(ContainerInterface $container){
			AppFactory::setContainer($container);

			return AppFactory::create();
		});

		$container->set(Helper::class, function(){
			return new Helper();
		});

		// Router
		$container->set(RouteParser::class, function(ContainerInterface $container){
			$app = AppFactory::createFromContainer($container);

			return $app->getRouteCollector()
					   ->getRouteParser();
		});

		// View
		$container->set(Twig::class, function(ContainerInterface $container){
			$settings = $container->get(SiteSettings::class);

			return Twig::create($settings->storage->views, [
				'cache' => ($_ENV['ENVIRONMENT'] == 'DEBUG' || $_ENV['ENVIRONMENT'] == 'DEV') ? false : $settings->storage->cache,
			]);
		});

		// Setting up Eloquent
		$container->set(Manager::class, function() : Manager{
			$capsule = new Manager();
			$capsule->addConnection([
										'driver'    => $_ENV['DB_DRIVER'],
										'host'      => $_ENV['DB_HOST'],
										'database'  => $_ENV['DB_NAME'],
										'username'  => $_ENV['DB_USER'],
										'password'  => $_ENV['DB_PASS'],
										'charset'   => 'utf8mb4',
										'collation' => 'utf8mb4_unicode_ci',
										'prefix'    => $_ENV['DB_PREFIX'],
									]);

			$capsule->setAsGlobal();
			$capsule->bootEloquent();
			Manager::schema()
				   ->defaultStringLength(191);

			return $capsule;
		});

		$container->set(ResponseFactoryInterface::class, static function(ContainerInterface $container){
			$app = $container->get(App::class);

			return $app->getResponseFactory();
		});

		// And add this entry
		$container->set(JwtAuth::class, function(ContainerInterface $container){
			$config = $container->get(SiteSettings::class)->auth;

			$issuer     = $config->issuer;
			$lifetime   = $config->lifetime;
			$privateKey = $config->privateKey;
			$publicKey  = $config->publicKey;

			return new JwtAuth($issuer, $lifetime, $privateKey, $publicKey);
		});

		$container->set(ValidationExceptionMiddleware::class, function(ContainerInterface $container){
			$factory = $container->get(ResponseFactoryInterface::class);

			return new ValidationExceptionMiddleware($factory, new ErrorDetailsResultTransformer(), new JsonEncoder());
		});

		$container->set(Translator::class, static function(ContainerInterface $container){
			return new Translator($container->get(SiteSettings::class));
		});

		$container->set(LoggerInterface::class, function(ContainerInterface $c){
			$settings = $c->get(SiteSettings::class);

			$logger = new Logger($settings->logger->name);

			$processor = new UidProcessor();
			$logger->pushProcessor($processor);

			$handler = new StreamHandler($settings->storage->logs, $settings->logger->level);
			$logger->pushHandler($handler);

			return $logger;
		});

	}
}
