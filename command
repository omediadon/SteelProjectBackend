#!/usr/bin/env php
<?php
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;

require __DIR__ . '/vendor/autoload.php';

class Command{
	const MIGRATIONS_PATH = __DIR__ . "/database/Migrations";
	const SEEDS_PATH      = __DIR__ . "/database/Seeds";
	const LOG_INFO        = 'i';
	const LOG_ERROR       = 'e';
	const LOG_WARNING     = 'w';
	const LOG_SUCCESS     = 's';
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
									'engine'    => 'InnoDB',
								]);
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
		Manager::schema()
			   ->defaultStringLength(191);
	}

	function exec(){
		$time_start = microtime(true);
		$this->log('Script start', self::LOG_INFO);
		$this->log(PHP_EOL);
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
		$this->log('Done!', self::LOG_SUCCESS);
		$time_end = microtime(true);
		$this->log("Total Execution Time: " . sprintf('%.3f', $time_end - $time_start) . 's');
	}

	private function help(){
		echo "\n";
		echo "syntaxis: php novice <command> [<args>]" . PHP_EOL;
		echo PHP_EOL;
		echo "Commands: \n";
		echo "php command --help                  -->   Displays the help menu." . PHP_EOL;
		echo "php command migrate                 -->   Migrate the database." . PHP_EOL;
		echo "php command seed                    -->   Seed the database tables." . PHP_EOL;
		echo "php command migrate --seed          -->   Migrate and seed the database." . PHP_EOL;
		echo PHP_EOL;
	}

	private function runMigrations(){
		$this->log('Started Migrating', self::LOG_SUCCESS);
		$files = glob(self::MIGRATIONS_PATH . '/*.php');
		$this->run($files);
		$this->log('Finished Seeding', self::LOG_SUCCESS);
		$this->log( PHP_EOL);
	}

	private function run($files, $isMigration = true){
		foreach($files as $file){
			$class = basename($file, '.php');
			if($isMigration){
			    $class = 'Database\\Migrations\\' . $class;
			}
			else{
			    $class = 'Database\\Seeds\\' . $class;
			}
			$this->log("\u{00B7}Processing: " . $class, self::LOG_INFO);
			$obj = new $class();
			$obj->run();
		}
	}

	private function runSeed(){
		$this->log("Started Seeding", self::LOG_SUCCESS);
		$files = glob(self::SEEDS_PATH . '/*.php');
		$this->run($files, false);
		$this->log('Finished Seeding', self::LOG_SUCCESS);
		$this->log( PHP_EOL);
	}

	private function log($str, $type = null){
		echo match ($type) {
			self::LOG_ERROR => "\033[31m$str \033[0m\n",              //error
			self::LOG_SUCCESS => "\033[32m$str \033[0m\n",            //success
			self::LOG_WARNING => "\033[33m$str \033[0m\n",            //warning
			self::LOG_INFO => "\033[36m$str \033[0m\n",               //info
			default => $str,
		};
	}
}

$novice = new Command($argv);
$novice->exec();
