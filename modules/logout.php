<?php
	$root = '../';
	require($root . 'includes/common.php');
	session_destroy();
	header("Location: " . $root . "index.php");
	
?>