<?php

namespace System\Config;

class TerraSession{
	public string $name        = "LfeSession";
	public string $lifetime    = "1 day";
	public bool   $secure      = true;
	public bool   $httpOnly    = true;
	public bool   $autorefresh = true;
	public array  $iniSettings = [];

	/**
	 * TerraSession constructor.
	 */
	public function __construct(){
	}

}
