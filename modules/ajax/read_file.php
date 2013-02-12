<?php

	if (isset($_POST['file_to_read']))
	{
		$root = '../../';
		require($root . 'includes/common.php');
		
		$ftp = configure_FTP();
		echo utf8_encode($ftp->ftp_get_contents($_POST['file_to_read']));	
	}

?>