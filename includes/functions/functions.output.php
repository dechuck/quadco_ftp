<?php

	######################
	## PLAYER FUNCTIONS ##
	######################
	
	function playerBreakdownQuery($encounter, $player) {
	
		global $_DB;
		
		$result = $_DB->query("SELECT p.`ID` AS `Player`, p.`Name`, p.`Server`, p.`Class`, p.`Race`, p.`Gender`, p.`Level`,
							   e.`Attempt`, e.`Kill`, UNIX_TIMESTAMP(e.`Start`) AS `Start`, UNIX_TIMESTAMP(e.`End`) AS `End`,
							   el.`Name` AS `Encounter`, et.`Health_10N`, et.`Health_10H`, et.`Health_25N`, et.`Health_25H`,
							   ez.`Name` AS `Zone`, e.`Parse`, e.`ID`, e.`File`, l.`Folder`, e.`Players`, s.`Name` AS `Realm`,
							   g.`Name` AS `Guild`, u.`Username`, el.`Main`, UNIX_TIMESTAMP(l.`Date`) AS `Created`, p.`Spec_1_Active`,
							   p.`Spec_1_Primary`, p.`Spec_1_Talents`, p.`Spec_2_Active`, p.`Spec_2_Primary`, p.`Spec_2_Talents`,
							   p.`Achievements`, gr.`Name` AS `Rank`, r.`Short` AS `Region`, pp.`Spec` AS `Spec_ID`, gs.`Name` AS `Spec`
							   FROM `" . DB_TABLE_PARSE_ENCOUNTERS . "` AS e
							   LEFT JOIN `" . DB_TABLE_PARSE_LIST . "` AS l ON e.`Parse` = l.`ID`
							   LEFT JOIN `" . DB_TABLE_ENCOUNTER_ZONES . "` AS ez ON e.`Zone` = ez.`ID`
							   LEFT JOIN `" . DB_TABLE_ENCOUNTER_LIST . "` AS el ON e.`Encounter` = el.`ID`
							   LEFT JOIN `" . DB_TABLE_ENCOUNTER_TRIGGERS . "` AS et ON el.`Main` = et.`ID`
							   LEFT JOIN `" . DB_TABLE_USERS . "` AS u ON l.`User` = u.`ID`
							   LEFT JOIN `" . DB_TABLE_PARSE_PLAYERS . "` AS p ON LOWER(p.`Name`) = '" . strtolower($player) . "'
							   LEFT JOIN `" . DB_TABLE_PARSE_PARTICIPANTS . "` AS pp ON pp.`Player` = p.`ID` AND pp.`Encounter` = '{$encounter}'
							   LEFT JOIN `" . DB_TABLE_GAME_SPECS . "` AS gs ON gs.`ID` = pp.`Spec`
							   LEFT JOIN `" . DB_TABLE_PARSE_GUILDS . "` AS g ON p.`Guild` = g.`ID`
							   LEFT JOIN `" . DB_TABLE_PARSE_GUILDS_RANKS . "` AS gr ON gr.`Guild` = g.`ID` AND gr.`Rank` = p.`Rank`
							   LEFT JOIN `" . DB_TABLE_SERVER_LIST . "` AS s ON p.`Server` = s.`ID`
							   LEFT JOIN `" . DB_TABLE_SERVER_CATS . "` AS c ON c.`ID` = s.`Category`
							   LEFT JOIN `" . DB_TABLE_SERVER_REGIONS . "` AS r ON r.`ID` = c.`Region`
							   WHERE e.`ID` = '" . ( 0 + $encounter ) . "'
							   LIMIT 1");
							   /*AND u.`Server` = p.`Server`
							   LIMIT 1");*/
							  
			if( !is_array($result) ) error('error', 'Error', 'Encounter not found.');
			
		return $result;
	}

	function playerBreakdown($_BREAKDOWN, $_TYPE, $_MERGE, &$_PLAYER) {

		global $_CONFIG, $_DB;
		
		$_PATH = str_replace('/test', '', $_CONFIG['PATH_ROOT']) . "users/{$_BREAKDOWN['Username']}/{$_BREAKDOWN['Folder']}/";
		$_TYPE = strtolower($_TYPE);
		$_FILE = '_new_api_' . str_replace('.json', '', $_BREAKDOWN['File']) . '_' . strtolower($_BREAKDOWN['Name']) . '_' . $_TYPE . '.json';

		if( file_exists($_PATH . 'cache/' . $_FILE) && $_CONFIG['CACHE'] == false ) {
		
			return json_decode(openFile($_PATH . 'cache/' . $_FILE, ( 1024 * 1024 )), true);

		} else {
		
			$_JSON 		= json_decode(openFile($_PATH . $_BREAKDOWN['File']), true);

			$_RETURN    = array('auras' => array(), 'data' => array(), 'info' => array(), 'health' => array(), 'players' => $_JSON['players'], 'spells' => array());
			$_ABILITIES = array();
			$_MOBS		= array();
			$_OVERALL	= array();

			$_PLAYER  	= -1;
			$_LAST		= -1;
			$_PERSEC    = 0;
			$_PERSEC_S  = array();

			while( list($key, $data) = each($_JSON['players']) ) {

				if( strtolower($data['n']) == strtolower($_BREAKDOWN['Name']) ) {
				
					$_PLAYER = $key;
					
					$_RETURN['info']['player'] = $key;
					$_RETURN['info']['spec']   = $data['s'];
					
					break;
				}
				
			}
			
			// $_JSON['data'] = $_JSON['data'][0];
				
			$_LAST = array_keys($_JSON['data']);
			$_LAST = str_replace('s', '', $_LAST[count($_LAST) - 1]);
				
			for( $_SECOND = 0; $_SECOND <= $_LAST; $_SECOND++ ) {
				
				while( list($_T, $_D) = @each($_JSON['data']['s' . $_SECOND]) ) {
				
					for( $i = 0; $i < count($_D); $i++ ) {
				
						# SOURCE OR TARGET
						$_SKEY 	 = str_replace(array('rm-', 'rp-'), '', $_D[$i]['s']);
						$_DKEY 	 = str_replace(array('rm-', 'rp-'), '', $_D[$i]['d']);

						$_SOURCE = ( is_numeric($_SKEY) && ( $_SKEY == $_PLAYER || ( $_JSON['players'][$_SKEY]['o'] == $_PLAYER && $_MERGE ) ) ? true : false );
						$_DEST   = ( is_numeric($_DKEY) && $_DKEY == $_PLAYER ? true : false );
						
						# SPELL MANAGEMENT

						@list($_ID, $_TICK) = explode('-', $_D[$i]['id']);

						if( !array_key_exists($_ID, $_RETURN['spells']) && ( $_SOURCE || $_DEST ) ) {

							$_SPELL	= $_DB->query("SELECT `id`, `name`, `icon`, `school` FROM `" . DB_TABLE_CACHE_SPELLS . "` WHERE `ID` = '" . $_ID . "' LIMIT 1", 300);
							$_RETURN['spells'][$_ID] = $_SPELL;

						} else if( $_SOURCE || $_DEST )
							$_SPELL = $_RETURN['spells'][$_ID];

							# MODULES
							switch($_T) {
							
								case 'auras':
								
									if( $_DEST ) {
										typeAuras($_D[$i], $_SECOND, $_RETURN['auras']);
									}
									
								break;
								
								case 'damage':
								case 'healing':
								
									if( $_SOURCE && $_TYPE == $_T ) {
										
										$_RETURN['spells'][$_ID]['o'] = 1;

										$_RETURN['info']['count']    += 1;
										$_RETURN['info']['total']    += $_D[$i]['v'];
										$_RETURN['info']['active']    = $_D[$i]['a'];

										$_PERSEC	+= $_D[$i]['v'];
										$_PERSEC_S[] = $_ID . '|' . str_replace('en-', '', $_DKEY) . '|' . $_D[$i]['v'];
										
										$_RETURN['players'][str_replace('en-', '', $_DKEY)]['ta'] = 1;
										
										typeGeneral($_D[$i], $_SPELL, $_TYPE, $_RETURN['data']);

									}
									
									if( is_numeric($_SKEY) && $_TYPE == $_T )
										$_OVERALL[( $_JSON['players'][$_SKEY]['o'] >= 0 ? $_JSON['players'][$_SKEY]['o'] : $_SKEY )] += $_D[$i]['v'];
									
									if( !is_numeric($_DKEY) && $_T == 'damage' ) 
										$_RETURN['health'][$_RETURN['players'][str_replace('en-', '', $_DKEY)]['db']] += $_D[$i]['v'];
								
								break;
								
								case 'deaths':
								
									if( $_DEST ) {
										$_RETURN['deaths'][] = array('s' => $_SECOND, 't' => 'de');
									}
								
								break;
								
								case 'resurrect':
								
									if( $_DEST ) {
										$_RETURN['deaths'][] = array('s' => $_SECOND, 't' => 're', 'p' => $_JSON['players'][$_SKEY]['n']);
									}
								
								break;
								
								case 'events':
								
									$_RETURN['events'][] = $_D[$i];
								
								break;
							
							}
						
					}
					
					set_time_limit(30);
				
				}
				
				# MAX PER SECOND
				if( !isset($_RETURN['info']['max']) || $_PERSEC > $_RETURN['info']['max'] ) {
				
					$_RETURN['info']['max'] 	   = $_PERSEC;
					$_RETURN['info']['max_time']   = $_SECOND;
					$_RETURN['info']['max_spells'] = $_PERSEC_S;
					
				}
				
				# ADD TO GRAPH
				$_RETURN['graph'][] = array($_SECOND, $_PERSEC);
				
				$_PERSEC   = 0;
				$_PERSEC_S = array();
				
			}
			
			# SORT ARRAYS MAX TO MIN
			uasort($_RETURN['data'], function($a, $b) { if( $a['total'] == $b['total'] ) return 0; return( $a['total'] < $b['total'] ) ? 1 : -1; });
			uasort($_RETURN['spells'], function($a, $b) { if( $a['name'] == $b['name'] ) return 0; return( $a['name'] < $b['name'] ) ? -1 : 1; });
			
			while( list($spell, $data) = each($_RETURN['data']) ) { uasort($_RETURN['data'][$spell]['targets'], function($a, $b) { if( $a['total'] == $b['total'] ) return 0; return( $a['total'] < $b['total'] ) ? 1 : -1; }); }
			reset($_RETURN['data']);
			
			# GET POSITION + ABOVE/BELOW
			arsort($_OVERALL);
			$_K = array_keys($_OVERALL);			
			$_F = array_search($_PLAYER, $_K);
			
				# ASSIGN POSITIONS
				if( $_F == 0 ) {
				
					$_RETURN['info']['position'] = array(
						array('p' => ( $_F + 1 ), 'id' => $_PLAYER, 'v' => $_OVERALL[$_PLAYER]),
						array('p' => ( $_F + 2 ), 'id' => $_K[( $_F + 1 )], 'v' => $_OVERALL[$_K[( $_F + 1 )]]),
						array('p' => ( $_F + 3 ), 'id' => $_K[( $_F + 2 )], 'v' => $_OVERALL[$_K[( $_F + 2 )]])
					);
					
				} else if( $_F == ( count($_K) - 1 ) ) {
				
					$_RETURN['info']['position'] = array(
						array('p' => ( $_F - 1 ), 'id' => $_K[( $_F - 2 )], 'v' => $_OVERALL[$_K[( $_F - 2 )]]),
						array('p' => ( $_F + 0 ), 'id' => $_K[( $_F - 1 )], 'v' => $_OVERALL[$_K[( $_F - 1 )]]),
						array('p' => ( $_F + 1 ), 'id' => $_PLAYER, 'v' => $_OVERALL[$_PLAYER])
					);
				
				} else {
				
					$_RETURN['info']['position'] = array(
						array('p' => ( $_F + 0 ), 'id' => $_K[( $_F - 1 )], 'v' => $_OVERALL[$_K[( $_F - 1 )]]),
						array('p' => ( $_F + 1 ), 'id' => $_PLAYER, 'v' => $_OVERALL[$_PLAYER]),
						array('p' => ( $_F + 2 ), 'id' => $_K[( $_F + 1 )], 'v' => $_OVERALL[$_K[( $_F + 1 )]])
					);
				
				}
	
			# CACHE IT!
			buildCache($_PATH, $_FILE, $_RETURN);
			
			return $_RETURN;

		}
		
		return false;
	
	}
	
	###########################
	## GLOBAL TYPE FUNCTIONS ##
	###########################
	
	function typeAuras($_DATA, $_SECOND, &$_ARRAY) {
	
		global $_RETURN;

		$_DATA['s'] = str_replace(array('rm-', 'rp-', 'en-'), '', $_DATA['s']);
		$_DATA['d'] = str_replace(array('rm-', 'rp-', 'en-'), '', $_DATA['d']);
						
		switch($_DATA['a']) {
						
			case 'Ap':
							
				if( !array_key_exists($_DATA['id'], $_ARRAY) ) {
								
					$_ARRAY[$_DATA['id']] = array(
									
						'a' => $_SECOND,
						'd' => 0,
						's' => $_DATA['v'],
						't' => $_DATA['t']
									
					);
								
				} else {
								
					$_ARRAY[$_DATA['id']]['a'] = $_SECOND;
								
				}
								
			break;
							
			case 'Re':
							
				if( array_key_exists($_DATA['id'], $_ARRAY) )
					$_ARRAY[$_DATA['id']]['d'] = ( $_SECOND - $_ARRAY[$_DATA['id']]['a'] );
							
			break;
							
			case 'Ad':
			case 'Rd':
							
				if( array_key_exists($_DATA['id'], $_ARRAY) )
					$_ARRAY[$_DATA['id']]['v'] = $_DATA['v'];

			break;
						
		}
	
	}
	
	function typeGeneral($_DATA, $_SPELL, $_TYPE, &$_ARRAY) {
		
		$_DATA['s'] = str_replace(array('rm-', 'rp-', 'en-'), '', $_DATA['s']);
		$_DATA['d'] = str_replace(array('rm-', 'rp-', 'en-'), '', $_DATA['d']);
		
		unset($_SPELL['o']);
		
		if( !array_key_exists($_DATA['id'], $_ARRAY) ) {
		
			$_ARRAY[$_DATA['id']] = array('total' => 0, 'count' => 0, 'max' => 0);
			
		}
	
			list($_VALUE, $_OVER) = explode('|', $_DATA['v']);
		
			# ADD OVERALL DETAILS
			$_ARRAY[$_DATA['id']]['total']  += $_VALUE;
			$_ARRAY[$_DATA['id']]['count']  += 1;
			$_ARRAY[$_DATA['id']]['average'] = round( ( $_ARRAY[$_DATA['id']]['total'] / $_ARRAY[$_DATA['id']]['count'] ), 2 );
			$_ARRAY[$_DATA['id']]['type'][strtolower($_DATA['t'])] += 1;
			
			if( strtolower($_DATA['t']) != 'hi' && strtolower($_DATA['t']) != 'ct' )
				$_ARRAY[$_DATA['id']]['type']['mi'] += 1;
									
			if( $_ARRAY[$_DATA['id']]['min'] > $_VALUE || !isset($_ARRAY[$_DATA['id']]['min']) )
				$_ARRAY[$_DATA['id']]['min'] = (int)$_VALUE;
													
			if( $_ARRAY[$_DATA['id']]['max'] < $_VALUE )
				$_ARRAY[$_DATA['id']]['max'] = (int)$_VALUE;

				# ADD TARGET DETAILS
				$_TARGET = $_DATA['d'];

				$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['total']  += $_VALUE;
				$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['count']  += 1;
				$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['average'] = round( ( $_ARRAY[$_DATA['id']]['targets'][$_TARGET]['total'] / $_ARRAY[$_DATA['id']]['targets'][$_TARGET]['count'] ), 2 );
				$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['type'][strtolower($_DATA['t'])]++;
													
				if( $_ARRAY[$_DATA['id']]['targets'][$_TARGET]['min'] > $_VALUE || !isset($_ARRAY[$_DATA['id']]['targets'][$_TARGET]['min']) )
					$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['min'] = $_VALUE;
														
				if( $_ARRAY[$_DATA['id']]['targets'][$_TARGET]['max'] < $_VALUE || !isset($_ARRAY[$_DATA['id']]['targets'][$_TARGET]['max']) )
					$_ARRAY[$_DATA['id']]['targets'][$_TARGET]['max'] = $_VALUE;
	
	}
	
	#######################
	## GENERAL FUNCTIONS ##
	#######################
	
	function buildCache($dir, $file, $data) {
	
		if( is_array($data) ) { $data = json_encode($data); }
		
		$handle = fopen($dir . 'cache/' . $file, 'w');
		fwrite($handle, $data);
		fclose($handle);
		
		return true;
	
	}

	function buildBreadcrumbs($array) {
	
		global $_TEMPLATE;
		
		$array = array_reverse($array, true);
		$array['Home'] = '/home';
		$array = array_reverse($array, true); 
		
		while( list($title, $url) = each($array) ) {
		
			$_TEMPLATE->assign_block_vars('breadcrumbs', array(
				'TITLE' => $title,
				'URL'	=> $url
			));
		
		}
	
	}
	
	function buildTypeSwitcher($array, $base) {
	
		global $_TEMPLATE;
		
		while( list($title, $details) = each($array) ) {
		
			$_TEMPLATE->assign_block_vars('view_switch', array(
					
				'ID' 	 => 'type-' . $details['url'],
				'NAME' 	 => $title,
				'ICON' 	 => 'images/icons/' . $details['icon'],
				'TYPE'	 => 'switchtype'
				
			));
		
		}
	
	}
	
	function buildShort($type) {
	
		$ex    = explode(' ', $type);
		$short = '';

		for( $i = 0; $i < count($ex); $i++ ) {
			
			$short .= strtolower(substr($ex[$i], 0, 1));
		
		}

		return $short;
	
	}
	
	function buildSpec($_BREAKDOWN) {
	
		global $_DB;
	
		$spec_1 = ( $_BREAKDOWN['Spec_1_Primary'] == $_BREAKDOWN['Spec'] ? true : false );
		$spec_2 = ( $_BREAKDOWN['Spec_2_Primary'] == $_BREAKDOWN['Spec'] ? true : false );

		$return = array();
		
		if( $spec_1 && !$spec_2 ) {
		
			$return['ID']	 = $_BREAKDOWN['Spec_ID'];
			$return['Name']  = $_BREAKDOWN['Spec_1_Primary'];
			$return['Value'] = str_replace('/', ' / ', $_BREAKDOWN['Spec_1_Talents']);
			$return['Role']	 = 'Unknown';
		
		} else if( !$spec_1 && $spec_2 ) {
		
			$return['ID']	 = $_BREAKDOWN['Spec_ID'];
			$return['Name']  = $_BREAKDOWN['Spec_2_Primary'];
			$return['Value'] = str_replace('/', ' / ', $_BREAKDOWN['Spec_2_Talents']);
			$return['Role']	 = 'Unknown';
		
		} else {
		
			$active = ( $_BREAKDOWN['Spec_1_Active'] == 1 ? '1' : '2' );
			$spec   = $_DB->query("SELECT `ID` FROM `" . DB_TABLE_GAME_SPECS . "` WHERE `Name` = '" . $_BREAKDOWN['Spec'] . "' AND `Class` = '" . Class2ID($_BREAKDOWN['Class']) . "' LIMIT 1");
		
			$return['ID']	 = $spec['ID'];
			$return['Name']  = $_BREAKDOWN['Spec_' . $active . '_Primary'];
			$return['Value'] = str_replace('/', ' / ', $_BREAKDOWN['Spec_' . $active . '_Talents']);
			$return['Role']	 = 'Unknown';
		
		}
		
		return $return;
	
	}
	
?>