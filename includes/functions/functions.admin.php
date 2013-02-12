<?php

	function admin_menuBuild($array, &$menu) {
	
		while( list($key, $value) = each($array) ) {
		
			if( is_array($value) ) {
			
				if( !array_key_exists(ucwords($key), $menu) ) { $menu[ucwords($key)] = array(); }
				admin_menuBuild($value, $menu[ucwords($key)]);
				
			} else { $menu[ucwords($key)] = $value; }
		
		}
	
	}
	
	function admin_menuTemplate($menu) {
	
		global $_TEMPLATE;
		
		while( list($cat, $links) = each($menu) ) {
		
			$_TEMPLATE->assign_block_vars('menucat', array('TITLE' => $cat));
			
			while( list($title, $link) = each($links) ) {
			
				$link = str_replace($_CONFIG['PATH_ROOT'], '', $link);
				$_TEMPLATE->assign_block_vars('menucat.item', array('TITLE' => $title, 'LINK' => $link));
			
			}
		
		}
	
	}
	
	function admin_getOption($name) {
	
		global $_CONFIG;
	
		$file   = $_CONFIG['PATH_ROOT'] . $_CONFIG['PATH_SUB'] . $_CONFIG['PATH_INCLUDE'] . 'options.json';
		$handle = fopen($file, 'r');
		$data   = fread($handle, filesize($file));
		$data   = json_decode($data, true);

		fclose($handle);
		
		for( $i = 0; $i < count($data); $i++ ) {
			if( $data[$i]['name'] == $name ) { return $data[$i]['value']; }
		}
		
		return false;
	
	}

?>