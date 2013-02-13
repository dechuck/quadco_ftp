<?php

  $_CONFIG = array();
  
	// General
	$_CONFIG['SITE_NAME']		= 'Quadco FTP';
	$_CONFIG['PHP_EXT']			= 'php';
	$_CONFIG['LOCALE_DEFAULT']	= 'en';
	$_CONFIG['COOKIE_DURATION']	= ( time() + ( 28 * 24 * 60 * 60 ) ); // 28 Days
	$_CONFIG['DEBUG']			= false;
	$_CONFIG['CACHE']			= true;
	
	/*********** CONFIGURATION A MODIFIER *************/
	
	// Informations de connexion à la DB
	
	$_CONFIG['DB_HOST']		= '';
	$_CONFIG['DB_USER']		= '';
	$_CONFIG['DB_PASS']		= '';
	$_CONFIG['DB_NAME']		= '';
	$_CONFIG['DB_PORT']		= '3306';
	
	// Path setup
	
	/**** Le path de la racine ftp du site au dossier ****/
	/* 'PATH JUSQUA LA RACINE FTP' . 'DOSSIER_ROOT' */
	$_CONFIG['BASE_FOLDER'] 	=  '/' . 'quadco_ftp/';
	
	
	/************* RIEN D'AUTRE A MODIFIER **************/
	
	// === Si vous voulez changer le dossier de base d'ou le script va se mettre === //
	// === a explorer le FTP, simplement changer cette configurations. ============= //
	// === Pourra etre ajoute au panel de configuration si necessaire ============== //
	$_CONFIG['PATH_FTP'] = '/';
	
	// Root path config
	$_CONFIG['PATH_SUB']		= '';
	$_CONFIG['PATH_ROOT'] 		= $_SERVER['DOCUMENT_ROOT'] . $_CONFIG['BASE_FOLDER'];
	
	
	// Autres configurations
	$_CONFIG['PATH_MODULES']  	= 'modules/';
	$_CONFIG['PATH_INCLUDE']  	= 'includes/';
	$_CONFIG['PATH_CACHE']		= $_CONFIG['PATH_INCLUDE'] . 'cache/';
	$_CONFIG['PATH_CLASS']		= $_CONFIG['PATH_INCLUDE'] . 'class/';
	$_CONFIG['PATH_CONFIG']		= $_CONFIG['PATH_INCLUDE'] . 'config/';
	$_CONFIG['PATH_LOCALE']		= $_CONFIG['PATH_INCLUDE'] . 'locale/';
	$_CONFIG['PATH_FUNCTIONS']	= $_CONFIG['PATH_INCLUDE'] . 'functions/';
	$_CONFIG['PATH_TEMPLATE'] 	= 'template/';
	$_CONFIG['PATH_IMAGES']		= 'images/';
	$_CONFIG['PATH_AVATAR']		= $_CONFIG['PATH_IMAGES'] . 'avatars/';
	$_CONFIG['PATH_SCRIPTS']	= 'static/';
	
	// Over-ride
	if( isset($_OVERRIDE) ) { while( list($K, $V) = each($_OVERRIDE) ) { $_CONFIG[$K] = $V; } }

?>