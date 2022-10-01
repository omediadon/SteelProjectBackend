<?php

namespace System\Utils;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use System\Config\SiteSettings;

final class MailUtils{
	/**
	 * @var SiteSettings
	 */
	private SiteSettings $siteSetup;

	/**
	 * @var PHPMailer
	 */
	private PHPMailer $mail;

	public function __construct(PHPMailer $PHPMailer, ContainerInterface $container){
		$this->siteSetup = $container->get(SiteSettings::class);
		$this->mail      = $PHPMailer;
	}

	/**
	 * @param string $to
	 * @param string $name
	 * @param string $reason
	 * @param string $content
	 * @param string $currenttime
	 * @param string $shortReason
	 * @param string $template
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function sendMail(string $to, string $name, string $reason, string $content, string $currenttime,
							 string $shortReason = "", string $template = "minimal") : bool{
		$storage     = $this->siteSetup->storage->emailTemplates;
		$imagePath   = $storage.'logo.png';
		$extension   = pathinfo($imagePath, PATHINFO_EXTENSION);
		$data        = file_get_contents($imagePath);
		$dataEncoded = base64_encode($data);
		$base64Str   = 'data:image/'.$extension.';base64,'.$dataEncoded;

		$temp = $storage.$template.".html";

		if(file_exists($temp)){
			$file = file_get_contents($temp);
			$file = str_replace("%%NAME%%", $name, $file);
			$file = str_replace("%%REASON%%", $reason, $file);
			$file = str_replace("%%CONTENT%%", $content, $file);
			$file = str_replace("%%SHORT_REASON%%", $shortReason, $file);
			$file = str_replace("%%CURRENTTIME%%", $currenttime, $file);
			$file = str_replace("%%LOGO%%", $base64Str, $file);
			$this->mail->isHTML(true);
		}
		else{
			$file = "Hello $name\n$reason\n\n$content\n\nThank you\n(C) 2020 - TerraBox";
			$this->mail->isHTML(false);
		}

		if($this->siteSetup->mail->isSmtp){
			$this->mail->isSMTP();
			$this->mail->Host       = $this->siteSetup->mail->server;
			$this->mail->SMTPAuth   = true;
			$this->mail->Username   = $this->siteSetup->mail->user;
			$this->mail->Password   = $this->siteSetup->mail->password;
			$this->mail->SMTPSecure = $this->siteSetup->mail->auth;
			$this->mail->Port       = $this->siteSetup->mail->port;
		}

		$this->mail->setFrom('noreply@terrabox.ga', 'NoReply TerraBox');
		$this->mail->addReplyTo('support@terrabox.ga', 'Support TerraBox');
		$this->mail->addAddress($to, $name);
		$this->mail->FromName = 'NoReply TerraBox';
		$this->mail->Subject  = $shortReason;
		$this->mail->msgHTML($file);

		return $this->mail->send();
	}
}