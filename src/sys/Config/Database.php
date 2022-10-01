<?php

namespace System\Config;

use Cake\Database\Driver\Mysql;
use PDO;

class Database{
	/**
	 * @var string
	 */
	public $driver = Mysql::class;
	/**
	 * @var string
	 */
	public $host = 'localhost';

	/**
	 * @var string
	 */
	public $database = 'omediato_life';
	//	public $database = 'dondevte_terrabox';
	/**
	 * @var string
	 */
	public $username = 'omediato_life';
	//	public $username = 'dondevte';
	/**
	 * @var string
	 */
	public $password = 'Asefthukom1*';
	//	public $password = '2TkzDul72Sp7';
	/**
	 * @var string
	 */
	public $encoding = 'utf8mb4';
	/**
	 * @var string
	 */
	public $collation = 'utf8mb4_unicode_ci';
	/**
	 * @var bool
	 */
	public $quoteIdentifiers = true;
	/**
	 * @var null|string
	 */
	public $timezone = null;
	/**
	 * @var array
	 */
	public $flags = [// Turn off persistent connections
					 PDO::ATTR_PERSISTENT         => false,
					 // Enable exceptions
					 PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					 // Emulate prepared statements
					 PDO::ATTR_EMULATE_PREPARES   => true,
					 // Set default fetch mode to array
					 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					 // Set character set
					 PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
	];
}
