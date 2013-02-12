<?php
	$root = '../';
	require($root . 'includes/common.php');
	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'Login Page',
		'ROOT'			=> $root,
	));
	$_TEMPLATE->set_filenames(array('body' => 'login.html'));

	if (isset($_SESSION['Usr_id']) && $_SESSION['Usr_class'] < 7)
	{
		$_TEMPLATE->set_filenames(array('body' => 'profile.html'));
		
			$_TEMPLATE->assign_vars(array(
			'USR_NAME' 		=> $_SESSION['Usr_name'],
		));
	}
	if (isset($_POST['Usr_name']))
	{
		$user = $_DB->query("SELECT Usr_id, Usr_name, Usr_hashpass, Usr_secret, Usr_class FROM T_Users WHERE Usr_name = '" . $_POST['Usr_name'] . "' LIMIT 1");
		if (count($user) > 0 && verify_pass($_POST['Usr_password'], $user['Usr_secret'], $user['Usr_hashpass']))
		{
			session_start();
			$_SESSION['Usr_id'] = $user['Usr_id'];
			$_SESSION['Usr_name'] = $user['Usr_name'];
			$_SESSION['Usr_class'] = $user['Usr_class'];
			header("Location: " . $root . "index.php");
		}
		else
		{
			die('Mauvaise combinaison d\'utilisateur et mot de passe');
		}
	}
			
	$_TEMPLATE->display('body');
	
?>