<?php
/******************************************************************************
 * Copyright (C) Omedia.top - All Rights Reserved 2020                        *
 *                                                                            *
 * Unauthorized copying of this file, via any medium is strictly prohibited   *
 * Proprietary and confidential                                               *
 *                                                                            *
 * Written by Omar SAKHRAOUI <webmaster@omedia.top>, 8/2020                   *
 ******************************************************************************/

namespace Config;

use App\Controllers\Home\HomeApiController;
use App\Controllers\Home\HomeBrowserController;
use App\Controllers\User\UserApiController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use System\Config\SiteSettings;
use System\Middlewares\JsonRequestMiddleware;

class RoutesFactory{
	private function __construct(){
	}

	static function setUpRoutes(App &$app){

		$settings = $app->getContainer()
						->get(SiteSettings::class);

		$app->get('[/]', HomeBrowserController::class . ':getHome')
			->setName('home');

		$app->group('/api', function(RouteCollectorProxy $group){
			$group->get('[/[info[/]]]', HomeApiController::class . ':index')
				  ->setName('api');

			$group->group('/users', function(RouteCollectorProxy $subGroup){
				$subGroup->get('[/]', UserApiController::class . ':index')
						 ->add(JsonRequestMiddleware::class);
				$subGroup->get('/all[/]', UserApiController::class . ':index')
						 ->add(JsonRequestMiddleware::class);
			})
				  ->add(JsonRequestMiddleware::class);

			$group->get("/{params:.*}[/]", HomeApiController::class . ":get404")
				  ->add(JsonRequestMiddleware::class);
		})
			->add(JsonRequestMiddleware::class);

		$app->get("/{params:.*}[/]", HomeBrowserController::class . ":get404");
		$app->map([
					  'PUT',
					  'POST',
					  'DELETE',
					  'PATCH',
					  'OPTIONS',
				  ], "/{params:.*}[/]", HomeApiController::class . ":get404")
			->add(JsonRequestMiddleware::class);

	}
}
