<?php

namespace System\Config;

use PHPMailer\PHPMailer\PHPMailer;

class Mail{
	public $isSmtp   = true;
	public $server   = 'mail.terrabox.ga';
	public $user     = 'noreply@terrabox.ga';
	public $password = 'asefthukom1';
	public $auth     = PHPMailer::ENCRYPTION_SMTPS;
	public $port     = 465;
	public $path     = "/bin/sendmail";
}