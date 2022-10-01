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

	public function __construct(SiteSettings $settings){
		$this->settings = $settings;
		$this->langFile = $settings->storage->lang;
		if(!$this->isExist()){
			/** @noinspection PhpUnhandledExceptionInspection */
			throw new Exception('Translator cant find the files.');
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

	public static function getAvailableLanguages(){
		return explode(',', $_ENV['AVAILABLE_LANGUAGES']);
	}

	public function getTheLanguage() : string{
		return $this->theLanguage;
	}

	public function setTheLanguage(string $lang) : void{
		$this->theLanguage = $lang;
	}

	public function t(string $word, string $lang = "") : string{
		if($lang == ""){
			$lang = $this->theLanguage;
		}

		$wordUpper = strtoupper($word);

		if(!key_exists($lang, $this->translationTable)){
			if($this->settings->apiInfo->debug){
				/** @noinspection PhpUnhandledExceptionInspection */
				throw new Exception("Missing Translation language  $lang", 0);
			}
		}

		$currLang = $this->translationTable[$lang];
		if(!key_exists($wordUpper, $currLang)){
			if($this->settings->apiInfo->debug){
				/** @noinspection PhpUnhandledExceptionInspection */
				throw new Exception("Missing Translation $word in $lang", 1);
			}

			$lang     = $this->defaultLanguage;
			$currLang = $this->translationTable[$lang];
			if(!key_exists($wordUpper, $currLang)){
				if($this->settings->apiInfo->debug){
					/** @noinspection PhpUnhandledExceptionInspection */
					throw new Exception("Missing Translation $word in $lang", 1);
				}

				return $word;
			}

			return $currLang[$wordUpper];
		}

		return $currLang[$wordUpper];
	}
}
