<?php

	function customException($exception) { error('error', 'Exception', $exception->getMessage()); }

    function memoryUsage($name, $peak = false) {

        if( $peak )
			$mem_usage = memory_get_peak_usage();
		else
			$mem_usage = memory_get_usage();
       
        echo "<strong>" . $name . "</strong>: ";
		
		if( $mem_usage < 1024 )
            echo $mem_usage . " bytes";
        else if( $mem_usage < 1048576 )
            echo round($mem_usage/1024,2)." kilobytes";
        else
            echo round($mem_usage/1048576,2)." megabytes";
           
        echo "<br />";
    }
	
	function openFile($file, $size = 4096) {
	
	  $data    = '';
	  $handle  = fopen($file, "r");
	 
	  if( $handle ) {

		while( !feof($handle) ) {
		  $buffer = fgets($handle, $size);
          if( !empty($buffer) ) $data .= $buffer;
		}

		fclose($handle);
	  }
	  
	 return $data;
	}
	
	function mdArrayCount($array) {
	  
	  $count = 0;
	  
	  while( list($key, $values) =  each($array) )
		$count += count($array[$key]);
	
	 return ( $count > 0 ? $count : 0 );
	}

	function recursiveDelete($str){
        if(is_file($str)){
            return @unlink($str);
        }
        elseif(is_dir($str)){
            $scan = glob(rtrim($str,'/').'/*');
            foreach($scan as $index=>$path){
                recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

	function formatString($string, $armory = false) {
	
		$string = strtolower(str_replace(array('.', '"', "'", '!', '?', ':', '-', "-", '_', '['. ']', '(', ')', '/', '\\', '&'), '', $string));
		
		// for( $i = 0; $i < strlen($string); $i++ ) { $string[$i] = iconv('ISO-8859-5', 'UTF-8', chr($string[$i])); }
		
		return str_replace(' ', ( $armory ? '-' : '' ), $string);
	}
	
	function getTime($seconds) {
	
		$min = floor( $seconds / 60 );
		$sec = ( $seconds % 60 );
	
		return $min . ':' . ( $sec < 10 ? '0' . $sec : $sec );
	}
	
	function maxNumber($number) {
	
		$max_no = '1'; for( $i = 0; $i < ( strlen($number) - 1 ); $i++ ) { $max_no .= '0'; }
		$max = ( ceil( ( $number / $max_no ) / 0.5 ) * 0.5 ) * $max_no;
	
	 return $max;
	}
	
	function generateURL($id, $title) {
	
		$title = strtolower(stripslashes($title));
		$title = formatString($title);
	
		return $id . '-' . $title;
	}

  function displayPaging($total, $limit, $pagenumber, $baseurl) {

	$showpages = "5"; // 1,3,5,7,9...
	
	// set up icons to be used
	$icon_path     =	'images/icons/';
	$icon_param    =	'align="top" style="border:0px;" ';
	$icon_first	   =	'first';
	$icon_last	   =	'last';
	$icon_previous =	'previous';
	$icon_next     =	'next';
	
	// do calculations
	$pages = ceil($total / $limit);
	$offset = ($pagenumber * $limit) - $limit;
	$end = $offset + $limit;

	// prepare paging links
	$html .= '<ul class="pagination">';
	// if first link is needed
	if($pagenumber > 1) { $previous = $pagenumber -1;
		$html .= '<li><a href="'.$baseurl.'1">'.$icon_first.'</a></li>';
	}
	// if previous link is needed
	if($pagenumber > 2) {    $previous = $pagenumber -1;
		$html .= '<li><a href="'.$baseurl.''.$previous.'">'.$icon_previous.'</a></li>';
	}
	// print page numbers
	if ($pages>=2) { $p=1;
		$pages_before = $pagenumber - 1;
		$pages_after = $pages - $pagenumber;
		$show_before = floor($showpages / 2);
		$show_after = floor($showpages / 2);
		if ($pages_before < $show_before){
			$dif = $show_before - $pages_before;
			$show_after = $show_after + $dif;
		}
		if ($pages_after < $show_after){
			$dif = $show_after - $pages_after;
			$show_before = $show_before + $dif;
		}   
		$minpage = $pagenumber - ($show_before+1);
		$maxpage = $pagenumber + ($show_after+1);

		while ($p <= $pages) {
			if ($p > $minpage && $p < $maxpage) {
				if ($pagenumber == $p) {
			    		$html .= "<li class=\"page\"><a href=\"#\">".$p."</a></li>";
				} else {
			    	$html .= '<li><a href="'.$baseurl.$p.'">'.$p.'</a></li>';
				}
			}
			$p++;
		}
	}
	// if next link is needed
	if($end < $total) { $next = $pagenumber +1;
		if ($next != ($p-1)) {
			$html .= '<li><a href="'.$baseurl.$next.'">'.$icon_next.'</a></li>';
		}
	}
	// if last link is needed
	if($end < $total) { $last = $p -1;
		$html .= '<li><a href="'.$baseurl.$last.'">'.$icon_last.'</a></li>';
	}
	$html .= "</ul>\n";
	
   return $html;
  }
 
	function niceTime($time) {
	  $delta = time() - $time;
	  if ($delta < 60) {
		return 'less than a minute ago.';
	  } else if ($delta < 120) {
		return 'about a minute ago.';
	  } else if ($delta < (45 * 60)) {
		return floor($delta / 60) . ' minutes ago.';
	  } else if ($delta < (90 * 60)) {
		return 'about an hour ago.';
	  } else if ($delta < (24 * 60 * 60)) {
		return 'about ' . floor($delta / 3600) . ' hours ago.';
	  } else if ($delta < (48 * 60 * 60)) {
		return '1 day ago.';
	  } else {
		return floor($delta / 86400) . ' days ago.';
	  }
	}
	
	function exception($ex) {
	
		error('error', 'Exception', $ex->getMessage() . '<br /><br />Line ' . $ex->getLine() . ' of ' . $ex->getFile());
	
	}
	
	function error($type, $title, $description) {
	
		global $template, $root;
		
		if( !$template ) { echo '<h2>' . $title . '</h2>' . $description; die; }
		
		$template->set_filenames(array('body' => 'error.html'));
		
		$template->assign_vars(array(
	
			'TITLE' => $title,
			'ROOT'	=> $root,
			
			'ERROR_CLASS' => strtolower($type),
			'ERROR_TITLE' => $title,
			'ERROR_BODY'  => $description
		
		));
		
		$template->display('body');
		die;
	
	}
	
	function setupDropdown($parse, $encounter) {
	
		global $template, $db, $type, $merge;
		
		$ub   = '';
		$drop = '';

		$result = $db->query("SELECT e.`ID`, el.`Name`, ez.`Name` AS `Zone`, e.`ID`, e.`Attempt`, e.`Kill`
							  FROM `" . DB_TABLE_PARSE_ENCOUNTERS . "` AS e
							  LEFT JOIN `" . DB_TABLE_ENCOUNTER_LIST . "` AS el ON el.`ID` = e.`Encounter`
							  LEFT JOIN `" . DB_TABLE_ENCOUNTER_ZONES . "` AS ez ON ez.`ID` = el.`Zone`
							  WHERE e.`Parse` = '" . $parse . "'");
	
			while( $enc = mysql_fetch_assoc($result) ) {
			
				if( !isset($zone) ) {
					$ub  .= '            <div class="hover item icon-left"><a href="#"><span class="ui-icon ui-icon-script"></span> ' . $enc['Zone'] . '</a></div>' . "\n";
					$zone = $enc['Zone'];
				}
				
				if( $enc['ID'] == $encounter ) {
					$list = $enc['Name'] . ' - ' . ( $enc['Name'] != 'Trash' ? ( $enc['Kill'] == 'Yes' ? 'Kill' : 'Attempt ' . $enc['Attempt'] ) : '' );
				}

				if( $enc['Name'] != 'Trash' ) $attempt = '_' . ( $enc['Kill'] == 1 ? 'kill' : 'attempt' . $enc['Attempt'] );
				
				$drop .= '                <li><a href="../view/' . $enc['ID'] . '-' . formatString($enc['Zone']) . '_' . formatString($enc['Name']) . $attempt . '-type_' . $type . '-merge_' . ( $merge ? 'on' : 'off' ) . '"><span class="ui-icon ' . ( $enc['Kill'] == 'Yes' ? 'ui-icon-check' : 'ui-icon-close' ) . '"></span>' . $enc['Name'] . '</a><span class="right">' . ( $enc['Name'] != 'Trash' ? ( $enc['Kill'] == 'Yes' ? 'Kill' : 'Attempt ' . $enc['Attempt'] ) : '' ) . '</span></li>' . "\n";
			
			}
			
			$ub .= '            <div class="hover item">' . "\n" . '              ' . $list . '<span class="ui-icon ui-icon-carat-1-s"></span>' . "\n" . '              <ul>' . "\n";
			$ub .= $drop;
			$ub .= '              </ul>' . "\n" . '            </div>' . "\n";
			
			$template->assign_vars(array('UB_NAV' => $ub));
	
	}
	
	function setupGuild($encounter) {
	
		global $template, $db, $root;
		
		$result = $db->query("SELECT pe.`Guild`, sl.`Name` AS `Server`, g.`ID`, g.`Name` AS `GuildName`, sr.`Short` AS `Region`
							  FROM `" . DB_TABLE_PARSE_ENCOUNTERS . "` AS pe
							  LEFT JOIN `" . DB_TABLE_PARSE_PARTICIPANTS . "` AS pa ON pa.`Encounter` = pe.`ID`
							  LEFT JOIN `" . DB_TABLE_PARSE_PLAYERS . "` AS pl ON pa.`Player` = pl.`ID`
							  LEFT JOIN `" . DB_TABLE_PARSE_GUILDS . "` AS g ON pl.`Guild` = g.`ID`
							  LEFT JOIN `" . DB_TABLE_SERVER_LIST . "` AS sl ON pl.`Server` = sl.`ID`
							  LEFT JOIN `" . DB_TABLE_SERVER_CATS . "` AS sc ON sc.`ID` = sl.`Category`
							  LEFT JOIN `" . DB_TABLE_SERVER_REGIONS . "` AS sr ON sr.`ID` = sc.`Region`
							  WHERE pe.`ID` = '" . $encounter . "'
							  GROUP BY pl.`Guild`");

			while( $row = mysql_fetch_assoc($result) ) {
			
				if( !isset($server) ) {
					$server = $row['Server'];
					$region = strtolower($row['Region']);
				}
				
				if( $row['Guild'] > 0 ) {
					$guilds[] = $row['GuildName'];
					break;
				} else {
					if( @!in_array(( empty($row['GuildName']) ? 'Unguilded' : $row['GuildName'] ), $guilds) )
					$guilds[] = ( empty($row['GuildName']) ? 'Unguilded' : $row['GuildName'] );
				}
				
				$_ID = ( $row['ID'] > 0 ? $row['ID'] : 0 );
			
			}

			switch(count($guilds)) {
				case 1:
					$guildName = '<a href="' . $root . 'guild/' . formatString($server) . '-' . $region . '/' . formatString($guilds[0]) . '">' . $guilds[0] . '</a>';
					if( $_ID ) $db->query("UPDATE `" . DB_TABLE_PARSE_ENCOUNTERS . "` SET `Guild` = $_ID WHERE `ID` = $encounter");
				break;
				case 2:
				case 3:
					$guildName = count($guilds) . ' Guilds';
				break;
				default:
					$guildName = 'Pick-up Group';
				break;
			}
			
			$template->assign_vars(array(
			
				'SERVER'	=> '<a href="' . $root . 'realm/' . formatString($server) . '-' . $region . '">' . $server . '</a>',
				'GUILD'		=> $guildName
				
			));
	
	}
	
	function setupLinks($base, $time = 0) {
	
		global $template, $root, $type, $merge;
	
			$template->assign_block_vars('typechange', array(
				'NAME'	=> ( $merge ? 'Un-' : '' ) . 'Merge Pets',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_' . $type . '-merge_' . ( $merge ? 'off' : 'on' ),
				'ICON'	=> ( $merge ? 'ui-icon-transfer-e-w' : 'ui-icon-shuffle' ),
				'CUR'	=> false
			));
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Raid Composition',
				'LINK'	=> $root . 'encounter/comp/' . $base,
				'ICON'	=> 'ui-icon-calculator',
				'CUR'	=> false
			));
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Damage [Outgoing]',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_damage-merge_' . ( $merge ? 'on' : 'off' ),
				'ICON'	=> 'ui-icon-arrowthickstop-1-n',
				'CUR'	=> ( 'damage' == $type ) ? true : false
			));
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Damage [Incoming]',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_damage_in-merge_' . ( $merge ? 'on' : 'off' ),
				'ICON'	=> 'ui-icon-arrowthickstop-1-s',
				'CUR'	=> ( 'damage_in' == $type ) ? true : false
			));
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Healing [Outgoing]',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_healing-merge_' . ( $merge ? 'on' : 'off' ),
				'ICON'	=> 'ui-icon-heart',
				'CUR'	=> ( 'healing' == $type ) ? true : false
			));
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Over-Healing [Outgoing]',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_overhealing-merge_' . ( $merge ? 'on' : 'off' ),
				'ICON'	=> 'ui-icon-trash',
				'CUR'	=> ( 'overhealing' == $type ) ? true : false
			));
			
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Mana Gains',
				'LINK'	=> $root . 'encounter/view/' . $base . '-type_gainsmana-merge_' . ( $merge ? 'on' : 'off' ),
				'ICON'	=> 'ui-icon-arrowreturnthick-1-s',
				'CUR'	=> ( 'gainsmana' == $type ) ? true : false
			));

			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Auras',
				'LINK'	=> $root . 'encounter/auras/' . $base,
				'ICON'	=> 'ui-icon-plus',
				'CUR'	=> ( 'auras' == $type ) ? true : false
			));
			
			$template->assign_block_vars('typechange', array(
				'NAME'	=> 'Log Browser',
				'LINK'	=> $root . 'encounter/browse/' . $base,
				'ICON'	=> 'ui-icon-zoomin',
				'CUR'	=> ( 'browse' == $type ) ? true : false
			));
	
	}
	
	function buildSpellCloud($spells, $url) {
	
		global $template;

		$no = 0; while( list($id, $spell) = each($spells) ) { $no += $spell['c']; } reset($spells);

		while( list($id, $spell) = each($spells) ) {
			
			$size = round(( ( $no / 2000 ) * $spell['c'] ), -1);
				
			$template->assign_block_vars('spells', array(
				
				'ID'	 => $id,
				'LINK'	 => $url . '?q=[spell=' . stripslashes($spell['n']) . ']',
				'NAME'	 => stripslashes($spell['n']),
				'ICON'	 => $spell['ic'],
				'SCHOOL' => $spell['s'],
				'SIZE'	 => ( $size < 15 ? 15 : ( $size > 30 ? 30 : $size ) ) . 'px'

			));
			
		}
	
	}
	
	function buildHealthBars($main, $size) {
	
		global $_CONFIG, $template, $db;
	
		$main = explode('|', $main);
		$css  = '    <style type="text/css">' . "\n";
		
		for( $i = 0; $i < count($main); $i++ ) {
		
			$result = $db->query("SELECT * FROM `" . DB_TABLE_ENCOUNTER_TRIGGERS . "` WHERE `ID` = '" . $main[$i] . "'");
			$boss   = mysql_fetch_assoc($result);
			
			$template->assign_block_vars('healthbars', array(
			
				'ID'	  => $main[$i],
				'NAME'	  => $boss['Name'], 
				'IMAGE'	  => str_replace("'", '', $boss['Name']) . '.png',
				'TOTAL'   => number_format($boss['Health_' . $size]),
				'CURRENT' => number_format($boss['Health_' . $size]),
			
			));
			
			$css .= "      div.boss-{$main[$i]}, div.boss-{$main[$i]} div.health { background-image: url('{$_CONFIG['PATH_IMAGES']}portraits/" . str_replace("'", '', $boss['Name']) . ".png'); }\n";

		}
		
		$css .= "    </style>\n";
		
		$template->assign_vars(array('CSSCODE' => $css));
	
	}

	function latestEncounters($start = 0, $limit = 5) {
	
		global $db, $template, $_SESSION;
		
		$result = $db->query("SELECT pe.`ID`, pe.`Attempt`, UNIX_TIMESTAMP(pe.`Start`) AS `Start`, UNIX_TIMESTAMP(pe.`End`) AS `End`,
							  pe.`Kill`, pe.`Players`, ez.`Name` AS `Zone`, el.`Name` AS `Encounter`
							  FROM `parse_encounters` AS pe
							  LEFT JOIN `encounter_zones` AS ez ON pe.`Zone` = ez.`ID`
							  LEFT JOIN `encounter_list` AS el ON pe.`Encounter` = el.`ID`
							  WHERE el.`Name` != 'Trash'
							  ORDER BY pe.`ID` DESC
							  LIMIT $start, $limit");
			while( $row = mysql_fetch_assoc($result) ) {
					
				$template->assign_block_vars('encounters', array(
				
					'ID' => $row['ID'],
					'ZONE' => $row['Zone'],
					'ENCOUNTER' => $row['Encounter'],
					'IMAGE' => str_replace("'", "", $row['Encounter']) . '.png',
					'PLAYERS' => str_replace(array('N', 'H'), array(' Normal', ' Heroic'), $row['Players']),
					'ATTEMPT' => $row['Attempt'],
					'KILL' => $row['Kill'],
					'URL' => $root . 'encounter/view/' . $row['ID'] . '-' . formatString($row['Zone']) . '_' . formatString($row['Encounter']) . '-type_damage-merge_' . ( $_SESSION['Merge'] == 'off' ? 'off' : 'on' ),
					'DURATION' => getTime(( $row['End'] - $row['Start'] ))
					
				));
			
			}
	
	}

	function getOnlineUsers() {

		if( $directory_handle = opendir(session_save_path()) ) {

			$count = 0;
			while( false !== ( $file = readdir( $directory_handle ) ) ) {

				if( $file != '.' && $file != '..' ) {
					if( time() - fileatime(session_save_path() . '\\' . $file) < MAX_IDLE_TIME * 60 ) {
						$count++;
					}
				}
				closedir($directory_handle);

				return $count;
				
			}

		} else
			return false;

	}
	
	function adminLogUpdate($title, $type, $original = false, $new = false) {
	
		global $db;
		
		
	
	}

?>