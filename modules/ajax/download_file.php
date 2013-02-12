<?php

	if (isset($_POST['file_to_download']))
	{
		$file = $_POST['file_to_download'];
		$file_name = $_POST['file_name'];
		// Set headers
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=" . $file_name);
		header("Content-Transfer-Encoding: binary");
		header('Cache-Control: max-age=0');
		
		$root = '../../';
		require($root . 'includes/common.php');
		
		$ftp = configure_FTP();
		
		// $files = explode('&', $file);
		// foreach ($files as $file)
		// {
			// utf8_encode($ftp->get($file));
			
			// readfile($file);
			// unlink($file);
		// }
		ob_clean();
		flush();
		$ftp->get($file, $file_name);
		readfile($file_name);
		// unlink($file);
	}

?>