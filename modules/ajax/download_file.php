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
		if ($_POST['is_img'])
		{
			$ftp->get_img($file, 'php://output');
		}
		else
		{
			$ftp->get($file, 'php://output');
		}
		readfile('php://output');
		// unlink($file);
	}

?>