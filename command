#!/usr/bin/env php
<?php
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager;
use System\Utils\Console;

require __DIR__ . '/vendor/autoload.php';

class Command{
	const MIGRATIONS_PATH = __DIR__ . "/database/Migrations";
	const SEEDS_PATH      = __DIR__ . "/database/Seeds";
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
		Console::log("Script start", color: 'bold', background_color: 'cyan');
		Console::log();
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
		Console::log();
		Console::log("Done!", color: 'bold', background_color: 'cyan');
		$time_end = microtime(true);
		Console::log("Total Execution Time: " . sprintf('%.3f', $time_end - $time_start) . 's', color: 'dim',
			background_color: 'cyan');
	}

	private function help(){
		echo "\n";
		echo "Syntax: php novice <command> [<args>]" . PHP_EOL;
		echo PHP_EOL;
		echo "Commands: \n";
		echo "php command --help                  -->   Displays the help menu." . PHP_EOL;
		echo "php command migrate                 -->   Migrate the database." . PHP_EOL;
		echo "php command seed                    -->   Seed the database tables." . PHP_EOL;
		echo "php command migrate --seed          -->   Migrate and seed the database." . PHP_EOL;
		echo PHP_EOL;
	}

	private function runMigrations(){
		Console::log('Started Migrating', 'green');
		$files = glob(self::MIGRATIONS_PATH . '/*.php');
		$this->run($files);
		Console::log('Finished Migrating', 'green');
		Console::log();
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
			Console::log("\u{00B7}Processing: " . $class, 'light_cyan');
			$obj = new $class();
			$obj->run();
		}
	}

	private function runSeed(){
		Console::log("Started Seeding", 'green');
		$files = glob(self::SEEDS_PATH . '/*.php');
		$this->run($files, false);
		Console::log('Finished Seeding', 'green');
	}

}

$novice = new Command($argv);
$novice->exec();
