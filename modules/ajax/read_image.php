<?php

	if (isset($_POST['path_to_img']))
	{
		$root = '../../';
		require($root . 'includes/common.php');
		unlinkRecursive('img_tmp');
		$ftp = configure_FTP();
		$ftp->get_img($_POST['path_to_img'], './img_tmp/' . $_POST['img_name']);
		echo 'modules/ajax/img_tmp/' . $_POST['img_name'];
	}

	function unlinkRecursive($dir) 
	{ 
		if(!$dh = @opendir($dir)) 
		{ 
			return; 
		} 
		while (false !== ($obj = readdir($dh))) 
		{ 
			if($obj == '.' || $obj == '..') 
			{ 
				continue; 
			} 

			if (!@unlink($dir . '/' . $obj)) 
			{ 
				unlinkRecursive($dir.'/'.$obj, true); 
			} 
		} 
		
	}
?>