<?php

namespace App\Controllers\User;

use App\Models\Entity\UserEntity;
use App\Models\UserModel;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use System\Controllers\BrowserController;
use System\Utils\Translator;

class UserBrowserController extends BrowserController{
	private UserModel $model;

	public function __construct(ContainerInterface $container, Translator $translator, UserModel $model){
		parent::__construct($container, $translator);
		$this->model = $model;
	}

	public function createUser(ServerRequest $request, Response $response, array $args = []) : Response{
		$this->request = $request;
		$hl            = $this->prepare();
		$response      = $response->withHeader("Content-Language", $hl);
		$title         = $this->siteSetup->apiInfo->name;

		$user = new UserEntity([
								   "name"     => "user1",
								   "email"    => "dsfsdf",
								   "password" => "sdfsdf",
							   ]);

		$ret = $this->model->addUser($user);

		$message = $ret["description"];

		$params = compact("message", "title");

		return $this->render($response, "file.twig", $params);
	}
}
