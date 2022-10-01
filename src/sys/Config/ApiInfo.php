<?php

namespace System\Config;

class ApiInfo{
	public string $version             = '1.0.0';
	public bool   $debug               = false;
	public bool   $dev                 = false;
	public string $name                = "";
	public string $logo                = "/assets/img/logo.png";
	public string $logobig             = "/assets/img/logobig.png";
	public string $coonectedCookieName = "connectedMail";

	public function __construct(){
		$this->version = $_ENV['SYSTEM_VERSION'];
		$this->name    = $_ENV['SITE_NAME'];
		$this->debug   = $_ENV['ENVIRONMENT'] == "DEBUG";
		$this->dev     = $_ENV['ENVIRONMENT'] == "DEBUG" || $_ENV['ENVIRONMENT'] == 'DEV';
	}
}
