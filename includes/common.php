<?php

	error_reporting(0);
	date_default_timezone_set ('America/Montreal');

	# CONFIG
	require('config/config.general.php');
	require('config/config.constants.php');

	# FUNCTIONS
	require('functions/functions.general.php');
	require('functions/functions.template.php');
	require('functions/functions.user.php');
	require('functions/functions.admin.php');
	require('functions/functions.php');
	require('functions/functions.inc.php');
	
	# ERROR HANDLING
	set_exception_handler('exception');
	
	# CLASSES
	require('class/class.locale.php');
	require('class/class.template.php');
	require('class/class.mysql.php');
	require('class/ftp.class.php');
	
	# START PAGE
	session_start();
	updateAccess();
	
	# CHECK USER CLASS AND LOGGED IN
	if (strpos($_SERVER['SCRIPT_FILENAME'], 'login.php') == false && $_SESSION['Usr_id'] <= 0 ) {
		header("Location: " . $_CONFIG['BASE_FOLDER'] . "login.php");
		
	} else if (strpos($_SERVER['SCRIPT_FILENAME'], '/admin') > 0 && (strpos($_SERVER['SCRIPT_FILENAME'], 'login.php') || ($_SESSION['Usr_id'] >= 0 && $_SESSION['Usr_class'] == 1))) {
	// } else if
		$_CONFIG['PATH_TEMPLATE'] .= 'admin/';
		// var_dump($_CONFIG['PATH_TEMPLATE']);
	}
	
	# INITIALISE _LOCALE
	$_LOCALE = new locale(( isset($_COOKIE['locale']) ? $_COOKIE['locale'] : $_CONFIG['LOCALE_DEFAULT'] ));
	
	# INITIALISE _TEMPLATE
	$_TEMPLATE = new template();
	
		$_TEMPLATE->set_template('', $_CONFIG['SITE_NAME']);
		
	# INITIALISE _DATABASE
	$_DB = new database($_CONFIG['DB_HOST'], $_CONFIG['DB_USER'], $_CONFIG['DB_PASS'], $_CONFIG['DB_NAME'], $_CONFIG['DB_PORT']);
	
		$_DB->cacheSet($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_CACHE']);
		
		unset($_CONFIG['DB_HOST']);
		unset($_CONFIG['DB_USER']);
		unset($_CONFIG['DB_PASS']);
		unset($_CONFIG['DB_NAME']);
		unset($_CONFIG['DB_PORT']);
		
		# BUILD HEADER AND FOOTER
		require($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_TEMPLATE'] . '_header.php');
		require($_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_TEMPLATE'] . '_footer.php');
	
	if ($_SESSION['Usr_id'] > 0)
	{
		set_ftp_info_session($_DB);
	}
	
	// BASIC SESSION SETUP
	$_SESSION['PATH_FTP'] = $_CONFIG['PATH_FTP'];

?>