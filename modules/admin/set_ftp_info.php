<?php
	$root = '../../';
	require($root . 'includes/common.php');

	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'Mettre à jour les infos de connexion FTP',
		'TAB' 		=> 'ftp_info',
		'ROOT'			=> $root,
	));

	if (isset($_POST['FTP_host']))
	{
		if (count($ftp = $_DB->query("SELECT FTP_id FROM T_FTP LIMIT 1")) > 0)
		{
			$_DB->update($_POST, array('FTP_id' => $ftp['FTP_id']), 'T_FTP');
		}
		else
		{
			$_DB->insert($_POST, 'T_FTP');
		}
		$_SESSION['FTP_HOST'] = $_POST['FTP_host'];
		$_SESSION['FTP_PORT'] = $_POST['FTP_port'];
		$_SESSION['FTP_USER'] = $_POST['FTP_user'];
		$_SESSION['FTP_PASS'] = $_POST['FTP_pass'];
	}

	$_TEMPLATE->assign_vars(array(
		'FTP_HOST' 		=> $_SESSION['FTP_HOST'],
		'FTP_PORT' 		=> $_SESSION['FTP_PORT'],
		'FTP_USER' 		=> $_SESSION['FTP_USER'],
	));

	$_TEMPLATE->set_filenames(array('body' => 'set_ftp_info.html'));
	$_TEMPLATE->display('body');
	
?>