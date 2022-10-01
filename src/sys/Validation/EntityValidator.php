<?php

namespace System\Validation;

use Psr\Container\ContainerInterface;
use System\Config\SiteSettings;
use System\Factories\QueryFactory;

abstract class EntityValidator{

	/**
	 * @var SiteSettings
	 */
	protected $siteSetup;

	/**
	 * @var QueryFactory
	 */
	protected $queryFactory;

	public function __construct(QueryFactory $queryFactory, ContainerInterface $container){
		$this->siteSetup    = $container->get(SiteSettings::class);
		$this->queryFactory = $container->get(QueryFactory::class);
	}
}