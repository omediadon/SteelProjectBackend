<?php

namespace System\Models;

use Psr\Container\ContainerInterface;
use System\Config\SiteSettings;
use System\Factories\QueryFactory;
use System\Utils\MailUtils;
use System\Utils\Translator;

abstract class EntityModel{
	protected const        PREFIX = "app_";
	protected QueryFactory $queryFactory;
	protected SiteSettings $siteSetup;
	protected MailUtils    $mailUtils;
	protected Translator   $translator;

	/**
	 * The constructor.
	 *
	 * @param MailUtils          $mailUtils
	 * @param QueryFactory       $queryFactory The query factory
	 * @param ContainerInterface $container
	 */
	public function __construct(MailUtils $mailUtils, QueryFactory $queryFactory, ContainerInterface $container){
		$this->queryFactory = $queryFactory;
		$this->mailUtils    = $mailUtils;
		$this->siteSetup    = $container->get(SiteSettings::class);
		$this->translator   = $container->get(Translator::class);
	}
}
