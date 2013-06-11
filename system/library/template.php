<?php
class Template 
{
	private $registry;
	
	public $data = array();
	
	private $name;
	private $file;
	
	private $template;
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function template()
	{
		return $this->template;
	}
	
	public function get_file()
	{
		return $this->file;
	}
	
	public function set_file($file_name)
	{
		$file = $this->theme->find_file($file_name . '.tpl');
		
		if ($file) {
			$this->file = $file;
		}
		else {
			if ($this->name) {
				$this->cache->delete('template' . $this->name);
			}
			
			trigger_error('Template::set_file(): Could not find file ' . $file_name . '.tpl! ' . get_caller());
			return false;
		}
		
		return true;
	}
	
	public function set_data($data)
	{
		$this->data = $data;
	}
	
	public function load($name, $data = array()){
		if ($this->name == $name) return;
		
		$this->name = $name;
		
		$this->data = $data;
	
		if (!$this->set_file($this->name)) {
			trigger_error("Unable to load template! " . get_caller());
			exit();
		}
	}
	
	public function render()
	{
		if (!$this->file) {
			trigger_error("No template was set!<br />" . get_caller() . "<br />" . get_caller(1));
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
			trigger_error('Error: Could not load template file ' . $this->file . '! ' . get_caller(1));
			exit();
		}
	}
	
	public function find_file($file)
	{
		if (!preg_match("/\\.tpl\$/", $file)) {
			$file .= '.tpl';
		}
		
		return $this->theme->find_file($file);
	}
}