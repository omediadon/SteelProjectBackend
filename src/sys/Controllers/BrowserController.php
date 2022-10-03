<?php

namespace System\Controllers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Csrf\Guard;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Routing\RouteParser;
use Slim\Views\Twig;
use SlimSession\Helper;
use System\Config\Asset;
use System\Config\SiteSettings;
use System\Utils\Translator;

abstract class BrowserController{
	protected ServerRequest $request;

	public function __construct(protected ContainerInterface $container, protected Translator $translator,
								protected RouteParser $router, protected Helper $session, protected Guard $csrf,
								protected SiteSettings $siteSetup){

	}

	/**
	 * @throws ContainerExceptionInterface
	 */
	final public function render(Response $response, string $file, array $params = []) : Response{
		$params = $this->prepareParams($params);

		return $this->container->get(Twig::class)
							   ->render($response, $file, $params);
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	private function prepareParams(array $params) : array{
		$settings    = $this->siteSetup;
		$sitename    = $_ENV['SITE_NAME'];
		$sitelogo    = $this->siteSetup->apiInfo->logo;
		$sitelogobig = $this->siteSetup->apiInfo->logobig;
		$__          = $this->translator;
		$router      = $this->router;

		$js  = [];
		$css = [];

		if(isset($params["assets"])){
			$this->prepareAssets($params, $css, $js);
		}

		return array_merge(compact("__", "settings", "router", "js", "css", "sitename", "sitelogobig", "sitelogo",), $params);
	}

	/**
	 * @param array $params
	 * @param array $css
	 * @param array $js
	 */
	private function prepareAssets(array &$params, array &$css, array &$js) : void{
		$newAssets          = [
			$this->siteSetup->assets->jQuery,
			$this->siteSetup->assets->bootstrap,
			$this->siteSetup->assets->popper,
			$this->siteSetup->assets->fontAwesome,
			$this->siteSetup->assets->simpleLineIcons,
		];
		$params["assets"]   = array_merge($newAssets, $params["assets"]);
		$params["assets"][] = $this->siteSetup->assets->siteWide;

		foreach($params["assets"] as $asset){
			assert($asset instanceof Asset);
			if($asset->css != null){
				foreach($asset->css as $acss){
					$css[] = $acss;
				}
			}
			if($asset->js != null){
				foreach($asset->js as $ajs){
					$js[] = $ajs;
				}
			}
		}

		unset($params["assets"]);
	}

	final public function redirect(Response $response, string $name, int $status = 302, array $params = []) : Response{
		if(empty($params)){
			return $response->withHeader('Location', $this->router->urlFor($name))
							->withStatus($status);
		}

		return $response->withHeader('Location', $this->router->urlFor($name, $params))
						->withStatus($status);
	}

	/**
	 * @return string
	 */
	final protected function prepare() : string{
		$params = $this->request->getQueryParams();

		$hl = $_ENV['DEFAULT_LANGUAGE'];

		if($this->request->hasHeader("Accept-Language")){
			$hq = $this->request->getHeaderLine("Accept-Language");
			if(strlen($hq) > 2){
				$hq = substr($hq, strpos($hq, ",") + 1, 2);
			}
			if(in_array($hq, Translator::getAvailableLanguages())){
				$hl = $hq;
			}
		}

		if($this->session->exists("hl")){
			$hl = $this->session->get("hl");
		}
		if(isset($params["hl"])){
			$hl = $params["hl"];
			$this->session->set("hl", $hl);
		}

		$this->translator->setTheLanguage($hl);

		return $hl;
	}
}
