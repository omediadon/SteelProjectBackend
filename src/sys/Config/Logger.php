<?php

namespace System\Config;

use Monolog\Logger as Log;

class Logger{
	public string $name                = "SauceLogger";
	public int    $level               = Log::DEBUG;
	public bool   $displayErrorDetails = true;
	public bool   $logErrors           = true;
	public bool   $logErrorDetails     = true;
}