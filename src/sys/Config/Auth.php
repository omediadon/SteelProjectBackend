<?php

namespace System\Config;

class Auth{
	/**
	 * @var string
	 */
	public $issuer = "TerraBox";
	/**
	 * @var int
	 */
	public $lifetime = 7776000;

	public function __construct(){
	}
}