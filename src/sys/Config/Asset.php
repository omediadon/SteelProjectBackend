<?php

namespace System\Config;

class Asset{
	public ?array $js;
	public ?array $css;

	/**
	 * Asset constructor.
	 *
	 * @param array|null $js
	 * @param array|null $css
	 */
	public function __construct(?array $js = [], ?array $css = []){
		$this->js  = $js;
		$this->css = $css;
	}
}