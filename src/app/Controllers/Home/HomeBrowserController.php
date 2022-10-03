<?php

namespace App\Controllers\Home;

use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Controllers\BrowserController;

final class HomeBrowserController extends BrowserController{
	public function getHome(ServerRequest $request, Response $response) : Response{
		$this->request = $request;
		$hl            = $this->prepare();
		$response      = $response->withHeader("Content-Language", $hl);
		$title         = 'Home';

		$assets = [$this->siteSetup->assets->landing];

		$icons = [
			[
				"icon"  => "icon-screen-desktop",
				"title" => $this->translator->t('HELLO'),
				"text"  => "This theme will look great on any device, no matter the size!",
			],
			[
				"icon"  => "icon-organization",
				"title" => "Fully Responsive",
				"text"  => "This theme will look great on any device, no matter the size!",
			],
			[
				"icon"  => "icon-chemistry",
				"title" => "Fully Responsive",
				"text"  => "This theme will look great on any device, no matter the size!",
			],
		];

		$testemonials = [
			[
				"image"  => "/assets/img/testimonials-1.jpg",
				"author" => "Fully Responsive",
				"text"   => "This theme will look great on any device, no matter the size!",
			],
			[
				"image"  => "/assets/img/testimonials-2.jpg",
				"author" => "Fully Responsive",
				"text"   => "This theme will look great on any device, no matter the size!",
			],
			[
				"image"  => "/assets/img/testimonials-3.jpg",
				"author" => "Fully Responsive",
				"text"   => "This theme will look great on any device, no matter the size!",
			],
		];

		$params = compact("title", "hl", "assets", "icons", "testemonials");

		return $this->render($response, "/pages/home.twig", $params);
	}

	public function get404(ServerRequest $request, Response $response) : Response{
		$this->request = $request;
		$hl            = $this->prepare();
		$response      = $response->withHeader("Content-Language", $hl);
		$title         = $this->siteSetup->apiInfo->name;

		$assets = [$this->siteSetup->assets->landing];

		$alerts = [
			[
				"title"  => "404",
				"type"   => "danger",
				"text"   => "Page was not found!.",
				"footer" => "<a href='/'>Back to home</a>",
			],
		];

		$params = compact("title", "hl", "assets", "alerts");

		return $this->render($response, "/pages/home.twig", $params);
	}
}
