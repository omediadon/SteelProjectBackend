<?php
declare(strict_types=1);

use System\App;

// Autoloader
require realpath(dirname(__DIR__)).'/vendor/autoload.php';

// Do the magic
(new App())->emit();