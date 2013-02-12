<?php

	$root = './';
	require($root . 'includes/common.php');
	
	$_TEMPLATE->set_filenames(array('body' => 'index.html'));
	$ftp = configure_FTP();
	
	// $html_struct = new HTML_Structure($ftp, "/quadcoFTP");
	$html_struct = new HTML_Structure($ftp, $_SESSION['PATH_FTP']);
	
	//var_dump($ftp->ftp_get_contents('login.php'));
	
	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'Quadco FTP',
		'TAB' 		=> 'Home',
		'ROOT'			=> $root,
		'HTML'			=> '<li rel="folder"><a>Quadco</a>' . $html_struct->html . '</li>',
	));
	
		
	
	$_TEMPLATE->display('body');
	
	
	// === UTILS === //
	
	// Creates html structure
	
	// function configure_FTP()
	// {
		 // $server="zestecrm.com";
		// /** FTP server port */
		 // $port=21;
		// /** FTP user */
		 // $user="zestecrm";
		// /** User specific directory (for zip and download) */
		 // $userDir="";
		// /** password */
		 // $password = "6p*N-f6JJPM.";
		
		 // // $server="ftp.Quadco.mobi";
		// // /** FTP server port */
		 // // $port=21;
		// // /** FTP user */
		// // $user="quadco007";
		// // /** User specific directory (for zip and download) */
		 // // $userDir="";
		// // /** password */
		 // // $password = "D0nt4get!";
		// /** FTP connection */
		 // $connection = "";
		// /** Passive FTP connection */
		 // $passive = false;
		// /** Type of FTP server (UNIX, Windows, ...) */
		 // $systype = "";
		// /** Binary (1) or ASCII (0) mode */
		 // $mode = 1;
		// /** Logon indicator */
		 // $loggedOn = false;
		// /** resume broken downloads */
		 // $resumeDownload = false;
		// /** temporary download directory on local server */
		 // $downloadDir = "";
		
		// // $base_dir = "/home/content/64/8016964/html/";
		// // $base_dir = "/home";
		// // $base_dir = "/quadcoFTP/";
		// $base_dir = "/public_html/quadcoFTP";
		
		// $ftp = new ftp($server, $port, $user, $password);
		// // echo $ftp->getCurrentDirectoryShort();
		// // var_dump($ftp->ftpRawList());
		// $ftp->setCurrentDir($base_dir);
		// //$ftp->setCurrentDir($base_dir);
		// // var_dump(ftp_raw($ftp->connection, 'MKD TEST'));
		// // var_dump(ftp_raw($ftp->connection, 'PORT a1,a2,a3,a4,p1,p2'));
		// // var_dump(ftp_raw($ftp->connection, 'CWD /html/home'));
		// // // var_dump(ftp_raw($ftp->connection, 'TYPE A'));
		// // // var_dump(ftp_raw($ftp->connection, 'PASV'));
		// // // var_dump(ftp_raw($ftp->connection, 'LIST -al'));
		// // var_dump(ftp_raw($ftp->connection, 'TYPE I'));
		// // var_dump(ftp_raw($ftp->connection, 'PASV'));
		// // var_dump(ftp_raw($ftp->connection, 'LIST -al'));
		// // var_dump($ftp->ftpRawList());
		
		// return $ftp;
		// //$top = $ftp->ftpRawList());
	// }
	
	// class Token
	// {
		// public $type;
		// public $token;
		
		// public function __construct($token)
		// {
			// $this->token = $token;
		// }
		
		// public function add_token()
		// {
			// return $token;
		// }
	// }
	
	// array(
		// "LI" =>
	// );
	function add_space($x) {
		return ($x <= 1) ? ' ' : ' ' . add_space($x - 1);
	}
	
	class HTML_Structure
	{
		
		private $level = 0;
		private $open_tag = 0;
		
		public $html = "";
		public $base_dir;
		public $actual_dir = "";
		public $actual_path = "";
		public $ftp;
			
		public function __construct($ftp, $path) 
		{
			$this->base_dir = $path;
			$this->actual_dir = $path;
			$this->ftp = $ftp;
			
			$this->run();
			//var_dump($this->html);
		}
		
		public function run() 
		{
			$this->actual_path = "";
			$this->html = $this->process_level($this->ftp->getCurrentDir() . '/', "");

			//Debugging purpose
			if ($level != 0 || $open_tag != 0)
			{
				die("Wrong parsing in HTML_Structure::run()<br> Contactez votre développeur si l'erreur se poursuit.");
			}
		}
		
		//Read a whole directory
		public function process_level($path, $html)
		{
			// var_dump($path);
			$content = $this->ftp->ftpRawList($path);
			// var_dump($path);
			// var_dump($content);
			$self = $this;
			
			$html .= $self->add_deep();
			
			// Anonymous function to bind $path with process elem and allow $fun to be used as fn for the fold
			$fun = function($html, $elem) use ($self, $path) {
				return $self->process_elem($html, $elem, $path);
			};
			
			// The fold
			return array_reduce($content, $fun, $html) . $this->end_deep();
		}
		
		// Process each elem and go in deeper if needed
		public function process_elem($html, $elem, $path)
		{
			$elem = new Elem($elem, $path);
			
			// Ajout du tag <li rel=" ... 
			$html .= $this->add_tag($elem);

			// Si c'est un directory, ca s'arrete ici
			if ($elem->prop['is_dir'] !== true)
				return $html . $this->close_tag();
			
			// Sinon, recusion all the way
			return $this->process_level($path . $elem->prop['name'] . '/', $html) . $this->close_tag();
		}
		
		
		// Adds <li></a>
		public function add_tag($elem) 
		{
			$this->open_tag++;
			//$class = ($elem->is_image()) ? 'class="view_image_action" meta-name="' . $elem->name . '" meta-path="' . $elem->path . '"' : '';
			return '<li ' . $elem->infos . ' rel="' . $elem->rel . '"><a>' . $elem->name . '</a>';
		}
		
		public function add_deep() 
		{
			$this->level++;
			return '<ul>';
		}
		
		public function end_deep()
		{
			$this->level--;
			return '</ul>';
		}
		
		public function close_tag() 
		{
			$this->open_tag--;
			return '</li>';
		}
		
		public function add_tag_rel($name, $rel = "default") 
		{
			$this->open_tag++;
			$this->html .= '<li rel="' . $rel . '"><a>' . $name . '<a>';
			return $this;
		}
		
		public function add_deep_rel() 
		{
			$this->level++;
			$this->html .= '<ul>';
			return $this;
		}
		
		public function end_deep_rel()
		{
			$this->level--;
			$this->html .= '</ul>';
			return $this;
		}
		
		public function close_tag_rel() 
		{
			$this->open_tag--;
			$this->html .= '</li>';
			return $this;
		}
		
		// Recurse around ftpRawList to get whole directories into an array base tree
		// public function scan_level($path, $array, $deep = 0)
		// {
			// $content = $this->ftp->ftpRawList($path);
			// $self = $this;
			
			// $fun = function($array, $elem) use ($path, $self, $deep) {
				// return $self->scan_elem($array, $elem, $path, $deep);
			// };
		
			// return array_reduce($content, $fun, $html);
		// }
		
		// public function scan_elem ($array, $elem, $path, $deep)
		// {
			// $elem = new Elem($elem);
			
			// echo add_space($deep) . $elem->prop['name'] . PHP_EOL;
				
			// // Si c'est un directory, ça s'arrête ici
			// if ($elem->prop['is_dir'] !== true)
			// {
				// return;
			// }
			
			// // Sinon, recusion all the way
			// return $this->scan_level($path . $elem->prop['name'] . '/', $array, $deep + 1);
			
		// }
		
		// public function process_level2($path, $html)
		// {
			// // var_dump($path);
			// // $origine = $this->actual_path;
			// // $this->actual_path += $path;
			// $this->actual_path = $path;
			// $content = $this->ftp->ftpRawList($path);
			// // $tmp = $this->add_deep() . array_reduce($content, "process_elem", $html) . $this->end_deep;
			// // $this->actual_path = $origine;
			// // return $tmp;
			// // $fun = function ($html, $elem) {
				// // return $this->process_elem($html, $elem); 
			// // };
			// $self = $this;
			// $fun = function($html, $elem) use ($self) {
				// $html .= $self->process_elem2($html, $elem);
				// return $html;
			// };
			// // var_dump($html);
			// $html .= $this->add_deep() . array_reduce($content, $fun, $html) . $this->end_deep();
			// var_dump(array_reduce($content, $fun, $html));
			// return $html;
		// }

		// public function process_elem2($html, $elem)
		// {
			// $elem = new Elem($elem);
			
			// $html .= $this->add_tag($elem->prop['name'], $elem->rel);
			// // var_dump($elem->prop['is_dir']);
			// if (!$elem->prop['is_dir'])
			// {
				// return $html . $this->close_tag();
			// }
			// $html .= $this->process_level2($this->actual_path . $elem->prop['name'] . '/', $html);
			// // var_dump($html);
		// }
	}
	
	// class ProcessTree {
		
		// public function __construct(
		
	// }
	/* Defines basic property of elements in the tree structure */
	class Elem {
		
		public $parent;
		
		public $prop = array();
		public $path = "";
		public $rel = "default";
		public $img_exts;
		public $txt_exts;
		public $infos;
		public function __construct($elem, $path = null)
		{
			/*$this->prop = array (
				"is_dir" => $elem['is_dir'],
				"extension" => $elem['extension'],
				"name" =>$elem['name"'],
				"perms" => $elem['perms'],
				"num" => $elem['num'],
				"size" => $elem['size'],
				"date" => $elem['date" => '],
				"is_link" => $elem['is_link'],
				"target" => $elem['target'],
			);
			*/
				
			$this->prop = $elem;
			$this->rel = ($this->prop['is_dir'] === true) ? 'folder' : 'default';
			
			// !!! will need some adjustment !!! //
			// $path = './' . substr($path, strlen('/QuacoFTP/'));
			$path = './' . substr($path, strlen($_SESSION['PATH_FTP'] . '/'));
			$this->path = $path . $this->name;
			
			$this->img_exts = array('.jpg', '.jpeg', '.png', '.gif', '.bmp');
			$this->txt_exts = array('.txt', '.php', '.js', '.css', '.html', '.phtml', '.xml', 'error_log',);
			$this->set_info();
		}
		
		public function __get($prop)
		{
			return $this->prop[$prop];
		}
		
		public function match_pattern($exts)
		{
			$found = 0;
			foreach ($exts as $ext)
			{
				$pattern = '/' . $ext . '$/';
				$found = preg_match($pattern, $this->name);
				if ($found != false)
					break;
			}
			
			return $found;
		}
		
		public function is_image()
		{
			return $this->match_pattern($this->img_exts);
		}
		
		public function is_textfile()
		{
			return $this->match_pattern($this->txt_exts);
		}
		
		public function set_info()
		{
			$infos = ' meta-name="' . $this->name . '" meta-path="' . $this->path . '"';
			$this->infos = 	($this->is_image()) ? 'class="view_image_action"' . $infos : 
							(($this->is_textfile()) ? 'class="view_textfile_action"' . $infos :
							$infos);
		}
	}
?>