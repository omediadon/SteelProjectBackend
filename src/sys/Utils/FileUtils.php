<?php

namespace System\Utils;

use System\Config\Storage;
use function substr;

class FileUtils{
	public static function avatarsPath(): string{
		return substr((new Storage())->avatarsFolder, 0, -1);
	}

	public static function avatarsPublicPath(): string{
		return substr((new Storage())->publicAvatars, 0, -1);
	}

	private function __construtor(){
	}
}
