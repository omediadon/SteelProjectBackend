<?php

namespace System\Config;

class Storage{
	public string  $avatarsFolder  = "public/upload/avatars/";
	public string  $publicAvatars  = "upload/avatars/";
	public string  $emailTemplates = "var/templates/email/";
	public string  $keys           = "var/keys/";
	public string  $logs           = "var/logs/app.log";
	public string  $cache          = "var/cache/";
	public string  $views          = "src/app/Views/";
	private string $home;

	public function __construct(){
		$this->home           = str_replace("src".DIRECTORY_SEPARATOR."sys".DIRECTORY_SEPARATOR."Config", "", __DIR__);
		$this->avatarsFolder  = $this->home.$this->avatarsFolder;
		$this->emailTemplates = $this->home.$this->emailTemplates;
		$this->keys           = $this->home.$this->keys;
		$this->logs           = $this->home.$this->logs;
		$this->cache          = $this->home.$this->cache;
		$this->views          = $this->home.$this->views;
	}
}