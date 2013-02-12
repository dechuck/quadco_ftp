<?php
	
	$root = '../../';
	require($root . 'includes/common.php');
	
	$_TEMPLATE->set_filenames(array('body' => 'index.html'));
	
	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'QuadcoFTP | Admin Panel',
		'TAB' 		=> 'Admin Panel',
		'ROOT'			=> $root,
	));
	
		
	
	$_TEMPLATE->display('body');
	
?>