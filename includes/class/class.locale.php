<?php

class locale {

	public  $current   = 'none';
	public  $variables = array();

	function __construct($location = false) {
	
		global $_CONFIG;

		if( !$location ) $location = isset($_COOKIE['locale']) ? $_COOKIE['locale'] : $_CONFIG['LOCALE_DEFAULT'];
		if( $this->_checkExist($location) ) {

			require($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_LOCALE'] . $location . '/locale.php');
		
			$this->current   = $location;
			$this->variables = $locale;
				  
			$this->_setCookie($location);

		} else
			throw new exception('The language file was either not set, or could not be found!');

	}
	
	function listAvailable($skipCur = false) {
	
		$langs	= array();
		
		$handle = opendir($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_LOCALE']);
		while( $file = readdir($handle) ) {
			if( ( $file != '.' && $file != '..' ) ) {
				$langs[] = $file;
			}
		}
		
		return $langs;
	}
	
	function getValue($name) {
	
		if( array_key_exists($name, $this->variables) ) {
		
			return $this->varirables;
		
		} else return false;
	
	}

	function getReplace($name, $replace) {
	
		$string = $this->getValue($name);
	 
		if( is_array($variables) && $string ) {
	   
			for( $i = 1; $i <= count($replace); $i++ ) {

				$string = str_replace('%r' . $i, $replace[$i], $string);
				
			}
	   
		} else if( $string ) {

			$string = str_replace('%r', $replace, $string);
			
		}
	
		return $string;
	}
	
	function _checkExist($locale) {

		global $_CONFIG;
	  
		if( file_exists($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_LOCALE'] . $locale . '/locale.php') )
			return true;
		else
			return false;

	}
	

	function _setCookie($locale) {

		global $_CONFIG;

		setcookie('locale', $locale, $_CONFIG['COOKIE_DURATION'], '/');
	}

}

?>