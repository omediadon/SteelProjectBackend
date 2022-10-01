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
	/**
	 * @var string
	 */
	public $privateKey;
	/**
	 * @var string
	 */
	public $publicKey;

	public function __construct(string $keysLocation){
		$this->publicKey  = file_get_contents($keysLocation."public.pem");
		$this->privateKey = file_get_contents($keysLocation."private.pem");
	}
}