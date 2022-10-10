<?php

namespace System\Controllers;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Validation\Factory;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteParser;
use SlimSession\Helper;
use System\Config\SiteSettings;
use System\Utils\StatusCodes;
use System\Utils\Translator;
use function call_user_func_array;
use function is_a;
use function method_exists;

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

	public function __construct(protected ContainerInterface $container, protected Manager $manager,
								protected Factory $validator){
		$this->router     = $this->container->get(RouteParser::class);
		$this->siteSetup  = $this->container->get(SiteSettings::class);
		$this->translator = $this->container->get(Translator::class);
	}

	final public function render(): Response{
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
		$this->payload["http"]["code"]    = $this->status;
		$this->payload["http"]["message"] = StatusCodes::getMessageForCode($this->status);
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
	final public function redirect(Response $response, string $name, int $status = 302, array $params = []): Response{
		return $response->withHeader('Location', $this->router->urlFor($name, $params))
						->withStatus($status);
	}

	/**
	 * @param ServerRequest $request
	 * @param Response      $response
	 *
	 * @return void
	 */
	final protected function prepare(ServerRequest $request, Response $response): void{
		$this->request  = $request;
		$this->response = $response;
		$params         = $this->request->getQueryParams();
		$hl             = $_ENV['DEFAULT_LANGUAGE'];
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
	}

	public function __call($method, $arguments){
		if(method_exists($this, 'the' . $method)){
			if(is_a($arguments[0], ServerRequest::class) && is_a($arguments[1], Response::class)){
				$this->prepare($arguments[0], $arguments[1]);

				 call_user_func_array([
												$this,
												'the' . $method,
											], $arguments);

				return $this->render();
			}
		}
	}
}
