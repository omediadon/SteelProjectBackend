#!/usr/bin/env php
<?php

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

class Command{
	const MIGRATIONS_PATH = __DIR__."/database/migrations";
	const SEEDS_PATH      = __DIR__."/database/seeds";
	private array $args;

	function __construct($args){
		$this->args = $args;

		// Fire up DotEnv
		$dotenv = Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		$dotenv->required([
							  'ENVIRONMENT',
							  'DB_DRIVER',
							  'DB_HOST',
							  'DB_NAME',
							  'DB_USER',
							  'DB_PASS',
							  'DB_PREFIX',
						  ]);

		// Set up Eloquent
		$capsule = new Manager();
		$capsule->addConnection([
									'driver'    => $_ENV['DB_DRIVER'],
									'host'      => $_ENV['DB_HOST'],
									'database'  => $_ENV['DB_NAME'],
									'username'  => $_ENV['DB_USER'],
									'password'  => $_ENV['DB_PASS'],
									'charset'   => 'utf8mb4',
									'collation' => 'utf8mb4_unicode_ci',
									'prefix'    => $_ENV['DB_PREFIX'],
									'engine' => 'InnoDB',
								]);

		$capsule->setAsGlobal();
		$capsule->bootEloquent();
		Manager::schema()->defaultStringLength(191);
	}

	function exec(){
		if(count($this->args) <= 1){
			$this->help();
		}
		else{
			switch($this->args[1]){
				case "migrate":
					$this->runMigrations();
					if(isset($this->args[2]) && $this->args[2] === '--seed'){
						$this->runSeed();
					}
					break;
				case "seed":
					$this->runSeed();
					break;
				case "help":
				case "--help":
					$this->help();
					break;
			}
		}
	}

	function help(){
		echo "\n";
		echo "syntaxis: php novice <command> [<args>]".PHP_EOL;
		echo PHP_EOL;

		echo "Commands: \n";
		echo "php command --help                  -->   Displays the help menu.".PHP_EOL;
		echo "php command migrate                 -->   Migrate the database.".PHP_EOL;
		echo "php command seed                    -->   Seed the database tables.".PHP_EOL;
		echo "php command migrate --seed          -->   Migrate and seed the database.".PHP_EOL;
		echo PHP_EOL;
	}

	function runMigrations(){
		$files = glob(self::MIGRATIONS_PATH.'/*.php');

		$this->run($files);
	}

	private function run($files){
		foreach($files as $file){
			require_once($file);

			$class = basename($file, '.php');

			$obj = new $class();
			$obj->run();
		}
	}

	function runSeed(){
		$files = glob(self::SEEDS_PATH.'/*.php');

		$this->run($files);
	}
}

$novice = new Command($argv);
$novice->exec();