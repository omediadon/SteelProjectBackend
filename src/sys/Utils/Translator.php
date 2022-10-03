<?php

namespace System\Utils;

use App\I18N;

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

	public function __construct(){
		$this->defaultLanguage  = $_ENV['DEFAULT_LANGUAGE'];
		$this->translationTable = I18N::getTranslations();
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
