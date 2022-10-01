<?php

use Slim\App;
use Slim\Middleware\Session;
use System\Config\SiteSettings;

/**
 * @var App $app
 */
$settings = $app->getContainer()
				->get(SiteSettings::class);

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

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();


// Middleware csrf
//$app->add(Guard::class);
