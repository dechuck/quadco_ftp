<?php

	$root = './';
	require($root . 'includes/common.php');
	
	$_TEMPLATE->set_filenames(array('body' => 'index.html'));
	
	// Configure FTP and connect
	$ftp = configure_FTP();
	
	// Build html struct that will be transform in explorer
	$html_struct = new HTML_Structure($ftp, $_SESSION['PATH_FTP']);
		
	$_TEMPLATE->assign_vars(array(

		'TITLE' 		=> 'Quadco FTP',
		'TAB' 		=> 'Home',
		'ROOT'			=> $root,
		'HTML'			=> $html_struct->html,
	));
	
	$_TEMPLATE->display('body');
	
	
	
	
	// === UTILS === //
	
	// '/list/of/dir/name' => will return 'name'
	// '/' => returns itself
	function get_name_rec($name)
	{
		$rec = substr(strstr($name, '/'), 1);
		if (!$rec || $rec == '')
			return $name;
		else
			return get_name_rec($rec);
	}
	
	
	function add_space($x) {
		return ($x <= 1) ? ' ' : ' ' . add_space($x - 1);
	}
	
	// Creates html structure
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
		}
		
		public function run() 
		{
			$this->actual_path = "";
			$this->html = '<li rel="folder"><a>' . get_name_rec($this->ftp->getCurrentDir()) . '</a>' . $this->process_level($this->ftp->getCurrentDir() . '/', "") . '</li>';

			//Debugging purpose
			if ($level != 0 || $open_tag != 0)
			{
				die("Wrong parsing in HTML_Structure::run()<br> Contactez votre développeur si l'erreur se poursuit.");
			}
		}
		
		//Read a whole directory
		public function process_level($path, $html)
		{
			$content = $this->ftp->ftpRawList($path);
			if ($content == '')
			{
				return $html;
			}
			$html .= $this->add_deep();
			
			$self = $this;
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
			$elem = new Elem($elem, $path, $this->ftp->currentDir);
			
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
	}
	
	
	// Defines basic property of elements in the tree structure
	class Elem {
		
		public $parent;
		
		public $prop = array();
		public $path = "";
		public $rel = "default";
		public $img_exts;
		public $txt_exts;
		public $infos;
		public function __construct($elem, $path = null, $path_ftp)
		{
				
			$this->prop = $elem;
			$this->rel = ($this->prop['is_dir'] === true) ? 'folder' : 'default';
			
			// If you want relative path
			// $path = str_replace('//', '/', './' . substr($path, strlen($path_ftp)));
			
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