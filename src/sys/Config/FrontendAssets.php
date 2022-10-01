<?php

namespace System\Config;

class FrontendAssets{
	public Asset $jQuery;
	public Asset $bootstrap;
	public Asset $popper;
	public Asset $fontAwesome;
	public Asset $simpleLineIcons;
	public Asset $landing;
	public Asset $siteWide;

	/**
	 * FrontendAssets constructor.
	 */
	public function __construct(){
		$this->jQuery          = new Asset(["/assets/js/jquery-3.5.1.min.js"]);
		$this->bootstrap       = new Asset(["/assets/js/bootstrap.min.js"], ["/assets/css/bootstrap.min.css"]);
		$this->popper          = new Asset(["/assets/js/popper.min.js"]);
		$this->landing         = new Asset(["/assets/js/landing.js"], ["/assets/css/landing.css"]);
		$this->siteWide        = new Asset(["/assets/js/script.js"], ["/assets/css/style.css"]);
		$this->fontAwesome     = new Asset(null, ["/assets/css/fontawesome.css"]);
		$this->simpleLineIcons = new Asset(null, ["/assets/css/simple-line-icons.css"]);
	}

}