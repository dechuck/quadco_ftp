<?php
class database {

  public  $_ID       = 0;
  public  $_COUNT    = 0;
  public  $_AFFECTED = 0;
  
  private $_CACHEDIR = 'cache/';
  private $_CONNECTION;
	function __construct($host, $user, $pass, $name, $port) {
		$this->_CONNECTION = @mysql_connect($host, $user, $pass, $port) or $this->error(mysql_error());
			
							 @mysql_select_db($name) or $this->error(mysql_error());
	}
	function __destruct() {
		mysql_close($this->_CONNECTION);
	}
	function query($query, $ttl = 0) {
	
		$this->_ID 		 = 0;
		$this->_AFFECTED = 0;
		//echo "<script type='text/javascript'>alert('test : $query');</script>";
		if( $ttl > 0 ) $data = $this->cacheGet(md5($query), $ttl);
		
		if( !isset($data) || !$data ) {

			$data   = array();
			$type 	= strtolower(substr($query, 0, 6));
			$size   = strtolower(substr($query, -7));
			$result = @mysql_query($query, $this->_CONNECTION) or $this->error(mysql_error(), $query);

			while( $row = @mysql_fetch_assoc($result) ) {
				$data[] = $row;
			}
			
			if( $size == 'limit 1' && count($data) == 1 ) $data = $data[0];
			if( $ttl > 0 ) $this->cacheWrite($data, md5($query), $ttl);
			if( $type == 'insert' ) 								   $this->_ID 		= mysql_insert_id();
			if( in_array($type, array('insert', 'update', 'delete')) ) $this->_AFFECTED = mysql_affected_rows();
			$this->_COUNT++;
		
		}
		return $data;
	}
	function insert($query, $table) {
		list($fields, $values) = $this->buildQuery('insert', $query);
		$this->query("INSERT INTO `{$table}` ( {$fields} ) VALUES ( {$values} )");
		return ( $this->_AFFECTED > 0 ? true : false );
	}
	
	function update($query, $where, $table) {
	
		if( is_array($where) ) $where = $this->buildQuery('select', $where);
	
		$this->query("UPDATE `{$table}`
					  SET " . $this->buildQuery('update', $query) . "
					  WHERE {$where}");
	
		return ( $this->_AFFECTED > 0 ? true : false );
	}
	function insertUpdate($query, $table) {
		list($fields, $values) = $this->buildQuery('insert', $query);
		$this->query("INSERT INTO `{$table}` ( {$fields} ) VALUES ( {$values} )
					  ON DUPLICATE KEY UPDATE " . $this->buildQuery('update', $query));
		return ( $this->_AFFECTED > 0 ? true : false );
	}
	function getCount($query, $ttl = 0) {
		$a = $this->query($query, $ttl);
		return ( $a[0]['Count'] > 0 ? $a[0]['Count'] : 0 );
	}
	
	function getFields($table, $ttl = 0) {
		
		$fields = array();
		$result = $this->query("SHOW FIELDS FROM `{$table}`", $ttl);
		
		while( list($k, $v) = each($result) ) { $fields[$v['Field']] = preg_replace('/\([0-9]+\)/', '', $v['Type']);
			$test = $v['Field'];
			//echo "<script type='text/javascript'>alert('test : $test');</script>";
		}
		
		return $fields;	
	}
	function buildQuery($type, $array) {
		switch($type) {
		
			case 'insert':
				$field = $value = null;

				while( list($f, $v) = each($array) ) {
					$field .= ( isset($field) ? ", " : "" ) . "`{$f}`";
					$value .= ( isset($value) ? ", " : "" );
					$value .= ( !is_numeric($v) && substr($v, 0, 5) != 'NOW()' && substr($v, 0, 5) != 'INET_' ? $this->escape($v, true) : $v );
				}
				return array($field, $value);
			
			break;
			case 'update':
				$query = "";

				while( list($f, $v) = each($array) ) {
					$v 		= ( !is_numeric($v) && substr($v, 0, 5) != 'NOW()' && substr($v, 0, 5) != 'INET_' ? $this->escape($v, true) : $v );
					$query .= ( strlen($query) > 0 ? ", " : "" ) . "`{$f}` = {$v}";
				}
				return $query;
			break;
			
			case 'select':
			
				$query = "";

				while( list($f, $v) = each($array) ) {

					$query .= ( strlen($query) > 0 ? " AND " : "" ) . "`{$f}` = '{$v}'";

				}

				return $query;
			
			break;
		}
	}
	
	function cacheSet($dir) {
	
		$this->_CACHEDIR = $dir;
	
	}
	
	function cacheGet($title, $ttl) {
	
		$file = $this->_CACHEDIR . 'sql_' . $title . '-' . $ttl . '.cache';
	
		if( file_exists($file) && ( filemtime($file) + $ttl ) >= time() ) {
		
			$data = file_get_contents($file);
			
			return unserialize($data);
		
		} else if( file_exists($file) ) {
		
			unlink($file);
		
		}
		
		return false;
	
	}
	
	function cacheWrite($data, $title, $ttl) {
	
		if( file_exists($this->_CACHEDIR) ) {
		
			$file = $this->_CACHEDIR . 'sql_' . $title . '-' . $ttl . '.cache';
			
			if( file_exists($file) ) unlink($file);
			
			$handle = fopen($file, 'w');
			fwrite($handle, serialize($data));
			fclose($handle);
			
		}
		
		return true;
	
	}
	function escape($var, $enclose = false) {
		if( is_array($var) ) {
			foreach( $var as &$val ) $val = $this->escape($val);
		} else if( is_string($var) ) {	
			if( strlen($var) > 0 ) {
				$var = @mysql_real_escape_string($var, $this->_CONNECTION);
				if( $enclose ) $var = "'{$var}'";
			} else
				
				return 'NULL';
		} else if( is_null($var) ) {
			
			$var = "NULL";
		} else if( is_bool($var) ) {
			$var = ($var) ? 1 : 0;
			
		}
		return $var; 
	}

	function error($error, $query = false) {
	
		$message  = "<pre><strong>MySQL Error</strong>: {$error}";
		if( $query ) $message .= "\n\n<strong>Original Query</strong>: {$query}\n";
		$message .= "</pre>";
		
		die($message);
	
	}
	
	function results($data, $number = 0) {

		echo "<table cellspacing=\"4\" cellpadding=\"4\">\n\n";
		
			if( count($data) > 0 ) {
			
				echo "  <thead>\n    <tr>\n";
			
					$fields = array_keys($data[0]);
					for( $i = 0; $i < count($fields); $i++ ) { echo "      <td>{$fields[$i]}</td>\n"; }
				
				echo "    </tr>\n  </thead>\n\n";

				echo "  <tbody>\n";
				
					for( $i = 0; $i < ( $number > 0 ? $number : count($data) ); $i++ ) {
					
						echo "    <tr>\n";
						
							while( list($field, $value) = each($data[$i]) ) { echo "      <td>{$value}</td>\n"; }
						
						echo "    </tr>\n";
					
					}
					
				echo "  </tbody>\n\n";
				
			} else {
			
				echo "  <tr><td><center><strong>No Results</strong></center></td></tr>\n";
			
			}

		echo "</table>\n\n";

	}
	
}
?>