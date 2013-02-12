<?php
	

function hash_pass($pass)
{
	$pass_arr['secret'] = $secret = generatePassword(8);
	$pass_arr['pass'] = md5($secret . $pass . $secret);
	return $pass_arr;
}

function verify_pass($pass, $secret, $hashpass)
{
	return (md5($secret . $pass . $secret) == $hashpass);
}

function configure_FTP()
{
	//var_dump($_SESSION);
	 // $server="zestecrm.com";
	// /** FTP server port */
	 // $port=21;
	// /** FTP user */
	 // $user="zestecrm";
	// /** User specific directory (for zip and download) */
	 // $userDir="";
	// /** password */
	 // $password = "6p*N-f6JJPM.";

	 $server=$_SESSION['FTP_HOST'];
	/** FTP server port */
	 $port=$_SESSION['FTP_PORT'];
	/** FTP user */
	 $user=$_SESSION['FTP_USER'];
	/** User specific directory (for zip and download) */
	 $userDir="";
	/** password */
	 $password = $_SESSION['FTP_PASS'];
	
	 // $server="ftp.Quadco.mobi";
	// /** FTP server port */
	 // $port=21;
	// /** FTP user */
	// $user="quadco007";
	// /** User specific directory (for zip and download) */
	 // $userDir="";
	// /** password */
	 // $password = "D0nt4get!";

	 /** FTP connection */
	 $connection = "";
	/** Passive FTP connection */
	 $passive = false;
	/** Type of FTP server (UNIX, Windows, ...) */
	 $systype = "";
	/** Binary (1) or ASCII (0) mode */
	 $mode = 1;
	/** Logon indicator */
	 $loggedOn = false;
	/** resume broken downloads */
	 $resumeDownload = false;
	/** temporary download directory on local server */
	 $downloadDir = "";

	// $base_dir = "/quadcoFTP/";
	// $base_dir = "/public_html/quadcoFTP";
	$ftp = new ftp($server, $port, $user, $password);
	$ftp->setCurrentDir($_SESSION['PATH_FTP']);
	return $ftp;
}

function set_ftp_info_session($_DB)
{
	$ftp = $_DB->query("SELECT * FROM T_FTP LIMIT 1");
	
	$_SESSION['FTP_HOST'] = $ftp['FTP_host'];	
	$_SESSION['FTP_PORT'] = $ftp['FTP_port'];
	$_SESSION['FTP_USER'] = $ftp['FTP_user'];
	$_SESSION['FTP_PASS'] = $ftp['FTP_pass'];
}

function format_post_for_email($array, $prefix)
{
	foreach ($array as $key => $value)
	{
		$key = str_replace($prefix, "", $key);
		$key = ucwords(str_replace("_", " ", $key));
		$strip_keys[$key] = $value;
	}
	return $strip_keys;
}

function strip_key($prefix, $key)
{
	$key = str_replace($prefix, "", $key);
	$key = ucwords(str_replace("_", " ", $key));
	
	return $key;
}

function email_info($info, $prefix, $mailto, $subject)
{
	$strip_info = format_post_for_email($info, $prefix);
	$message = "<pre style='font-size:14px;'>";
	foreach ($strip_info as $key => $value)
	{
		$message .= $key . " => " . $value . "\r\n";
	}
	$message .= "</pre>";
	$headers .= "From: RBCI Registration <rbciregistration@zesteincentive.com>" . "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n" . 
               "Content-type: text/html; charset=UTF-8" . "\r\n"; 
	mail($mailto, $subject, $message, $headers);
	// var_dump($message);
}

function strip_key_names($prefix)
{
}
	
?>