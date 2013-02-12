<?php

	function forceLogin($class = false) {
	
		global $_CONFIG;
	
		if( ( $class > 0 && $_SESSION['Class'] < $class ) || !isset($_SESSION['ID']) )
			header("Location: {$_CONFIG['PATH_ROOT']}/user/login");
		
		else updateAccess();
	
	}

	function updateAccess() {
	
		global $_DB;
		
		if( isset($_SESSION['ID']) && $_SESSION['ID'] > 0 ) {
							
			$_DB->query("UPDATE `" . DB_TABLE_USERS . "` SET `LastAccess` = NOW() WHERE `ID` = '" . $_SESSION['ID'] . "'");
			$forward = false;
			
		}

	}

	function regCheck($reg) {

		global $_DB, $_CONFIG, $_LOCALE, $_SESSION;
		
		if( $reg['user'] == '' || alphaNumeric($reg['user']) == false )
			$errors[] = $_LOCALE->variables['ERROR_REG_USERNAME_ALPHA'];

		if( strlen($reg['user']) < 3 || strlen($reg['user']) > 24 )
			$errors[] = $_LOCALE->variables['ERROR_REG_USERNAME_LENGTH'];
	
		if( userExist($reg['user']) )
			$errors[] = $_LOCALE->variables['ERROR_REG_USERNAME_EXIST'];
	
		if( $reg['pass'] == '' || alphaNumeric($reg['pass']) == false )
			$errors[] = $_LOCALE->variables['ERROR_REG_PASSWORD'];

		if( $reg['pass'] != $reg['passAgain'] )
			$errors[] = $_LOCALE->variables['ERROR_REG_PASSWORD_MATCH'];

		if( validEmail($reg['email']) == false )
			$errors[] = $_LOCALE->variables['ERROR_REG_EMAIL'];

		if( emailExist($reg['email']) )
			$errors[] = $_LOCALE->variables['ERROR_REG_EMAIL_EXIST'];
		
		return ( is_array($errors) ? $errors : false );
	}

	function logCheck($login, $app = false) {

		global $_DB, $_CONFIG, $locale;
	 
		$row = $_DB->query("SELECT `ID`, `Password`, `PasswordHash`, `Enabled`
						   FROM `" . DB_TABLE_USERS . "`
						   WHERE LOWER(`Username`) = '" . strtolower($login['user']) . "'
						   LIMIT 1");
		
			if( !$row )
				$errors[] = 'Username not found (' . $login['user'] . ')!';
		  
			if( !$app && $row['Password'] != md5($row['PasswordHash'] . $login['pass'] . $row['PasswordHash']) )
				$errors[] = 'Incorrect password';

			else if( $app && $row['PasswordHash'] != $login['pass'] )
				$errors[] = 'Incorrect password';
		  
			if( $row['Enabled'] == 'No' )
				$errors[] = 'This account has been disabled, please check your email for details';
		  
			if( $row['Enabled'] == 'Closed' )
				$errors[] = 'This account has been closed by it\'s owner';

	 return ( is_array($errors) ? $errors : false );
	}

	function userExist($username) {
	 global $_DB;
	 
		$t = ( (0+$username) > 0 ? 'ID' : 'Username' );
	 
		$a = $_DB->getCount("SELECT COUNT(*) AS `Count`
						    FROM `" . DB_TABLE_USERS . "`
						    WHERE LOWER(`" . $t . "`) = LOWER('" . $username . "')");
	  
	 return ( $a ? true : false );
	}
	
	function emailExist($email) {
	 global $_DB;
	
		$a = $_DB->getCount("SELECT COUNT(*) AS `Count`
							FROM `" . DB_TABLE_USERS . "`
							WHERE `Email` = '" . $email . "'");

	 return ( $a ? true : false );
	}
	
	function alphaNumeric($str) {

		return ( !preg_match("/^([-a-z0-9])+$/i", $str)) ? false : true;
	}



	function validEmail($email) {

		return @eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
	
	function clearCookie() {
	
	  setcookie('PHPSESSID', null, 1, '/');
	
	}

	function uploadAvatar($file, $current) {
	
		global $_CONFIG;
	
		if( !isset($file) || $file['size'] < 1 ) error('error', 'Upload Failed', 'No file information recieved.');
		if( $file['size'] > $_CONFIG['USER_AVATARMAX'] ) error('error', 'Upload Failed', 'File is too large.');
				
			$pp = pathinfo($filename = $file["name"]);
			if( $pp['basename'] != $filename ) error('error', 'Upload Failed', 'Invalid file name.');
			
				list($width, $height, $type, $attr) = getimagesize($file["tmp_name"]);

				if( !array_key_exists($type, $_CONFIG['USER_AVATARTYPE']) ) error('error', 'Upload Failed', 'File must be either PNG, JPG or GIF.');

				$target = md5(date('F j, Y, g:i a') . $file["name"]) . '.' . $_CONFIG['USER_AVATARTYPE'][$type];
				
				if( $current != 'default.avatar.png' ) @unlink('./' . $_CONFIG['PATH_AVATAR'] . $row['Avatar']);

					// Scale image to appropriate avatar dimensions
					$scaley    = $height / $_CONFIG['USER_AVATARMAXY'];
					$scalex    = $width  / $_CONFIG['USER_AVATARMAXX'];
					$scale     = ( $scaley < 1 && $scalex < 1 ) ? 1 : ( $scaley > $scalex ) ? $scaley : $scalex;
					$newwidth  = floor( $width  / $scale );
					$newheight = floor( $height / $scale );
						
					if( $_CONFIG['USER_AVATARTYPE'][$type] == 'gif' )
						$orig = imagecreatefromgif($file["tmp_name"]);
					else if( $_CONFIG['USER_AVATARTYPE'][$type] == 'jpg' )
						$orig = imagecreatefromjpeg($file["tmp_name"]);
					else if( $_CONFIG['USER_AVATARTYPE'][$type] == 'png' )
						$orig = imagecreatefrompng($file["tmp_name"]);
					  
						if( !isset($orig) ) error('error', 'Upload Failed', 'Image processing failed, please try resaving it.');

						$thumb = imagecreatetruecolor($newwidth, $newheight);	
						$transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
						imagefilledrectangle($thumb, 0, 0, $nWidth, $nHeight, $transparent);
						imagecopyresampled($thumb, $orig, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

						if( $_CONFIG['USER_AVATARTYPE'][$type] == 'gif' )
							imagegif($thumb, './' . $_CONFIG['PATH_AVATAR'] . $target);
						else if( $_CONFIG['USER_AVATARTYPE'][$type] == 'jpg' )
							imagejpeg($thumb, './' . $_CONFIG['PATH_AVATAR'] . $target, 85);
						else if( $_CONFIG['USER_AVATARTYPE'][$type] == 'png' )
							imagepng($thumb, './' . $_CONFIG['PATH_AVATAR'] . $target, 3);
						  
		return $target;
	}
	
	function generatePassword($length = 8) {

		$password  = "";
		$possible  = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
		$maxlength = strlen($possible);
	
		$inc = 0; 
    
		while( $inc < $length ) { 

			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
			if( !strstr($password, $char) ) { 
				$password .= $char;
				$inc++;
			}

		}

		return $password;

	}
	
	function makeSecret($length = 20) {

		$secret = substr(md5(date('F j, Y, g:i:s a')), 0, $length);
		return $secret;
	}

?>