<?php
class Template extends Library
{
	public $data = array();
	
	private $name;
	private $file;
	
	private $template;
	
	private $root_dir = null;
	private $theme_override = null;
	
	public function __construct($registry)
	{
		parent::__construct($registry);
	}
	
	public function template()
	{
		return $this->template;
	}
	
	public function get_file()
	{
		return $this->file;
	}
	
	public function setTheme($theme)
	{
		$this->theme_override = $theme;
	}
	
	public function setRootDirectory($dir)
	{
		$this->root_dir = $dir;
	}
	
	public function set_file($file_name)
	{
		$file = $this->theme->find_file($file_name . '.tpl', $this->theme_override, $this->root_dir);
		
		if ($file) {
			$this->file = $file;
		}
		else {
			if ($this->name) {
				$this->cache->delete('template' . $this->name);
			}
			
			trigger_error('Template::set_file(): Could not find file ' . $file_name . '.tpl! ' . get_caller(0, 2));
			return false;
		}
		
		return true;
	}
	
	public function setData($data)
	{
		$this->data = $data;
	}
	
	public function load($name, $theme = null, $admin = null){
		$this->name = $name;
		
		if (!$this->set_file($this->name, $theme, $admin)) {
			trigger_error("Unable to load template! " . get_caller());
			exit();
		}
	}
	
	public function render()
	{
		if (!$this->file) {
			trigger_error("No template was set!" . get_caller(0, 2));
			exit();
		}
		
		if (is_file($this->file)) {
			//TODO: Do we want plugins to modify templates in this way!?
			// The plugins can only modify for default template... does this make sense? just use a new template?
			// Maybe the plugin template overrides the default template (when requested by plugin)? 
			
			//if there are plugins that have modified this template,
			//we use the merged version of this file
			$file = $this->plugin->getFile($this->file);
		
			extract($this->data);
			
			ob_start();
			
			include($file);
			
			return ob_get_clean();
		}
		else {
			trigger_error('Error: Could not load template file ' . $this->file . '! ' . get_caller(0, 2));
			exit();
		}
	}
	
	public function find_file($file)
	{
		if (!preg_match("/\\.tpl\$/", $file)) {
			$file .= '.tpl';
		}
		
		return $this->theme->find_file($file, $this->theme_override, $this->root_dir);
	}
}