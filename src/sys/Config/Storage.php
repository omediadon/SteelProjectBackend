<?php

namespace System\Config;

class Storage{
	public string  $avatarsFolder  = "public" . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR . "avatars" .
									 DIRECTORY_SEPARATOR . "";
	public string  $publicAvatars  = "upload" . DIRECTORY_SEPARATOR . "avatars" . DIRECTORY_SEPARATOR . "";
	public string  $emailTemplates = "var" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "email" .
									 DIRECTORY_SEPARATOR . "";
	public string  $keys           = "var" . DIRECTORY_SEPARATOR . "keys" . DIRECTORY_SEPARATOR . "";
	public string  $logs           = "var" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "app.log";
	public string  $cache          = "var" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "";
	public string  $views          = "src" . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "Views" .
									 DIRECTORY_SEPARATOR . "";
	private string $home;

	public function __construct(){
		$this->home           = str_replace("src" . DIRECTORY_SEPARATOR . "sys" . DIRECTORY_SEPARATOR . "Config", "",
											__DIR__);
		$this->avatarsFolder  = $this->home . $this->avatarsFolder;
		$this->emailTemplates = $this->home . $this->emailTemplates;
		$this->keys           = $this->home . $this->keys;
		$this->logs           = $this->home . $this->logs;
		$this->cache          = $this->home . $this->cache;
		$this->views          = $this->home . $this->views;
	}
}
