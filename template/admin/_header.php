<?php

	/***************************
	** Assign General Details **
	***************************/
	
	$user_connected = (isset($_SESSION['Usr_id']) && $_SESSION['Usr_class'] < 7) ? 1 : 0;
	
	$_TEMPLATE->assign_vars(array(
		'SITE_NAME' 		=> $_CONFIG['SITE_NAME'],
		'CUR_LINK'  		=> $_SERVER['SCRIPT_NAME'],
		'USER_CONNECTED'  		=> $user_connected,
		'USER_CLASS'  		=> $_SESSION['Usr_class'],
		'USER_ID'  		=> $_SESSION['Usr_id'],
		'USER_NAME'  		=> $_SESSION['Usr_name'],
	));

?>