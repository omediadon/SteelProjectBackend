<?php

namespace System\Utils;

use Exception;
use System\Config\SiteSettings;

final class Translator{
	/**
	 * @var string
	 */
	private string $langFile = "";
	/**
	 * @var string
	 */
	private string $defaultLanguage;
	/**
	 * @var string
	 */
	private string $theLanguage;
	/**
	 * @var array
	 */
	private array $translationTable;
	/**
	 * @var false
	 */
	private bool $canTranslate = true;

	public function __construct(private SiteSettings $settings){
		$this->langFile = $this->settings->storage->lang;
		if(!$this->isExist()){
			$this->canTranslate=false;
		}
		$this->defaultLanguage  = $_ENV['DEFAULT_LANGUAGE'];
		$this->translationTable = include $this->langFile;
	}

	private function isExist() : bool{
		if(!file_exists($this->langFile)){
			return false;
		}
		if(!is_readable($this->langFile)){
			return false;
		}

		return true;
	}

	public static function getAvailableLanguages() : array{
		return explode(',', $_ENV['AVAILABLE_LANGUAGES']);
	}

	public function getTheLanguage() : string{
		return $this->theLanguage;
	}

	public function setTheLanguage(string $lang) : void{
		$this->theLanguage = $lang;
	}

	public function t(string $word, string $lang = "") : string{
		if(!$this->canTranslate)
			return $word;
		if($lang == ""){
			$lang = $this->theLanguage;
		}

		$wordUpper = strtoupper($word);

		if(!key_exists($lang, $this->translationTable)){
			return $word;
		}

		$currLang = $this->translationTable[$lang];
		if(!key_exists($wordUpper, $currLang)){


			$lang     = $this->defaultLanguage;
			$currLang = $this->translationTable[$lang];
			if(!key_exists($wordUpper, $currLang)){
				return $word;
			}

		}

		return $currLang[$wordUpper];
	}
}
