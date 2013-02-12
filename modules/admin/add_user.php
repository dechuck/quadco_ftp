<?php
	$root = '../../';
	require($root . 'includes/common.php');

	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'Ajouter un utilisateur',
		'TAB' 		=> 'add',
		'ROOT'			=> $root,
	));

	
	if (isset($_POST['Usr_name']))
	{
		if ($_POST['Usr_password'] != $_POST['Usr_password_confirm'])
			die ('Les mots de passe ne concorde pas');
		
		$pass = hash_pass($_POST['Usr_password']);
		$_POST['Usr_secret'] = $pass['secret'];
		$_POST['Usr_hashpass'] = $pass['pass'];
		unset($_POST['Usr_password']);
		unset($_POST['Usr_password_confirm']);
		$insert = $_POST;
		$_DB->insert($insert, 'T_Users');
	}
			
	$_TEMPLATE->set_filenames(array('body' => 'add_user.html'));
	$_TEMPLATE->display('body');
	
?>