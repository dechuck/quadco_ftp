<?php
	
	$root = '../';
	require($root . 'includes/common.php');
	
	$_TEMPLATE->set_filenames(array('body' => 'profile.html'));
	
	$_TEMPLATE->assign_vars(array(
		'TITLE' 		=> 'Quadco FTP | Profile',
		'TAB' 		=> 'Profile',
		'ROOT'			=> $root,
	));
	
	if (isset($_POST['new_password']))
	{
		if ($_POST['new_password'] != $_POST['new_password_confirm'])
			die ('Les mots de passe ne concorde pas');
		
		$pass = hash_pass($_POST['new_password']);
		$update['Usr_secret'] = $pass['secret'];
		$update['Usr_hashpass'] = $pass['pass'];
		$update_id['Usr_id'] = $_SESSION['Usr_id'];
		$_DB->update($update, $update_id, 'T_Users');
		
	}
	
	$_TEMPLATE->display('body');
	
?>