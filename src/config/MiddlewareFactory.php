<?php

namespace Config;

use Slim\Middleware\Session;
use System\Config\SiteSettings;

class MiddlewareFactory{
	private function __construct(){
	}

	static function setUpMiddlewares(&$app){

		$settings = $app->getContainer()
						->get(SiteSettings::class);

		if (php_sapi_name() !== "cli") {
			$sess = $settings->session;

			/**
			 * Session middleware
			 */
			$app->add(new Session([
									  'name'        => $sess->name,
									  'autorefresh' => $sess->autorefresh,
									  'secure'      => $sess->secure,
									  'lifetime'    => $sess->lifetime,
									  'httponly'    => $sess->httpOnly,
								  ]));
		}

		$app->addRoutingMiddleware();
		$app->addBodyParsingMiddleware();

		// Middleware csrf
		//$app->add(Guard::class);
	}
}
