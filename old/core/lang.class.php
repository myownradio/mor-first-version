<?php

class lang {

	protected $translations;

	function load_lang($lang = 'en') {
		$lang_file = $_SERVER['DOCUMENT_ROOT'] . "/lang/" . $lang . ".json";
		if( file_exists($lang_file) ) {
			$this->translations = json_decode(file_get_contents($lang_file));
		}
	}
	
	function tr() {
		return $this->translations;
	}

}