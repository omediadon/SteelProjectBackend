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
use App\Middlewares\JwtAdminMiddleware;
use App\Middlewares\JwtMiddleware;
use App\Middlewares\JwtRoleMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use System\Config\SiteSettings;
use System\Middlewares\JsonRequestMiddleware;

class RoutesFactory{
	private function __construct(){
	}

	static function setUpRoutes(App &$app){

		$app->get('[/]', HomeBrowserController::class . ':getHome')
			->setName('home');

		$app->group('/api', function(RouteCollectorProxy $group) use (&$app){
			$group->get('[/[info[/]]]', HomeApiController::class . ':index')
				  ->setName('api');

			$group->group('/users', function(RouteCollectorProxy $subGroup){
				$subGroup->get('[/]', UserApiController::class . ':index');
				$subGroup->get('/all[/]', UserApiController::class . ':index');
				$subGroup->get('/login[/]', UserApiController::class . ':login');
				$subGroup->get('/signup[/]', UserApiController::class . ':signup');
			});

			$group->group('/token', function(RouteCollectorProxy $subGroup) use (&$app){
				$subGroup->get('[/]', HomeApiController::class . ':getTestingToken');
				$subGroup->get('/protected[/]', HomeApiController::class . ':protectedRoute')
						 ->add(JwtMiddleware::class);
				$subGroup->get('/admin[/]', HomeApiController::class . ':protectedRoute')
						 ->add(JwtAdminMiddleware::class);
				$subGroup->get('/role[/]', HomeApiController::class . ':protectedRoute')
						 ->add(new JwtRoleMiddleware($app->getContainer(), 'member'));
			});

			$group->get("/{params:.*}[/]", HomeApiController::class . ":get404");
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
