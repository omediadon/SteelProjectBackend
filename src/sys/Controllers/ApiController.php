<?php

namespace System\Controllers;

use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteParser;
use SlimSession\Helper;
use System\Config\SiteSettings;
use System\Utils\StatusCodes;
use System\Utils\Translator;

abstract class ApiController{
	protected int                  $status  = StatusCodes::HTTP_OK;
	protected mixed                $data;
	protected SiteSettings         $siteSetup;
	protected RouteParserInterface $router;
	protected Helper               $session;
	protected Translator           $translator;
	protected ServerRequest        $request;
	protected Response             $response;
	private array                  $payload = [];

	public function __construct(protected ContainerInterface $container, protected Manager $manager){
		$this->router     = $this->container->get(RouteParser::class);
		$this->siteSetup  = $this->container->get(SiteSettings::class);
		$this->translator = $this->container->get(Translator::class);
	}

	final public function render(array $params = []) : Response{
		if(isset($this->data)){
			$this->payload["data"] = $this->data;
		}
		else{
			if($this->request->getMethod() == "GET"){
				$this->status = StatusCodes::HTTP_NOT_FOUND;
			}
			else{
				$this->status = StatusCodes::HTTP_NOT_ACCEPTABLE;
			}
		}
		$this->payload["error"]["code"]    = $this->status;
		$this->payload["error"]["message"] = StatusCodes::getMessageForCode($this->status);

		if(StatusCodes::canHaveBody($this->status)){
			$this->response = $this->response->withJson($this->payload, $this->status, JSON_PRETTY_PRINT);
			$this->response = $this->response->withHeader("Content-type", "application/json");
		}
		$this->response = $this->response->withStatus($this->status);

		return $this->response;
	}

	/**
	 * @param Response $response
	 * @param string   $name
	 * @param int      $status
	 * @param array    $params
	 *
	 * @noinspection PhpUnused
	 *
	 * @return Response
	 */
	final public function redirect(Response $response, string $name, int $status = 302, array $params = []) : Response{
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

		if(isset($params["hl"])){
			$hl = $params["hl"];
		}

		$this->translator->setTheLanguage($hl);

		return $hl;
	}
}
