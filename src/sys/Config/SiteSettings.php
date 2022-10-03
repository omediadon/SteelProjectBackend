<?php

namespace System\Config;

class SiteSettings{
	public Roles          $userRoles;
	public Mail           $mail;
	public ApiInfo        $apiInfo;
	public Storage        $storage;
	public Auth           $auth;
	public Database       $database;
	public TerraSession   $session;
	public Logger         $logger;
	public FrontendAssets $assets;

	public function __construct(){
		$this->userRoles = new Roles();
		$this->mail      = new Mail();
		$this->apiInfo   = new ApiInfo();
		$this->storage   = new Storage();
		$this->auth      = new Auth();
		$this->database  = new Database();
		$this->session   = new TerraSession();
		$this->logger    = new Logger();
		$this->assets    = new FrontendAssets();
	}
}

