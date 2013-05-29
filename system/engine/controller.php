<?php

abstract class Controller 
{
	protected $registry;
	protected $class _path;
	protected $children = array();
	public $output;
	public $template;
	public $data = array();
	public $error = array();
	
	public function __construct($class_path, &$registry)
	{
		$this->registry = &$registry;
		
		if($class _path)
{
			$this->class _path = $class_path;
			
			$this->template = new Template($registry);
		}
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function __set($key, $value)
	{
		$this->registry->set($key, $value);
	}
	
	public function _($key)
	{
		return $this->language->get($key);
	}

	protected function forward($route, $args = array()) {
		return new Action($route, $args);
	}
	
	protected function getBlock($context, $name, $args = array()){
		$block = $context . '/block/' . $name;
		
		if (!is_array($args)) {
			trigger_error('Error: In Controller ' . get_class ($this) . ' while retreiving block ' . $block . ' - $args passed to Controller::getBlock() must be an array of parameters to be passed to the block method');
			exit();
		}
		
		$params = array('settings' => $this->model_block_block->getBlockSettings($context . '/' . $name));
		
		foreach($args as $a)
{
			$params[] = $a;
		}
		
		$action = new Action($block);
		$file = $action->getFile();
		$class = $action->getClass();
		$class_path = $action->getClassPath();
		$method = $action->getMethod();
		
		if (file_exists($file)) 
{
			_require_once($file);

			$controller = new $class ($class_path, $this->registry);

			call_user_func_array(array($controller, $method), $params);
			
			return $controller->output;
		} else {
			trigger_error('Error: Could not load block ' . $block . '! The file was missing.');
		}
	}

	protected function getModule($name, $settings = array()){
		$module = 'module/' . $name;
		
		if (!is_array($settings)) {
			trigger_error('Error: ' . get_class ($this) . '::getModule(): $settings must be an array! Usage $this->getModule(\'module_name\', array($setting1, $setting2, ...))');
			echo get_caller(2);
			exit();
		}
		
		$action = new Action($module);
		$file = $action->getFile();
		$class = $action->getClass();
		$class_path = $action->getClassPath();
		$method = $action->getMethod();
		
		if (file_exists($file)) 
{
			_require_once($file);

			$controller = new $class ($class_path, $this->registry);

			call_user_func_array(array($controller, $method), array($settings));
			
			return $controller->output;
		} else {
			trigger_error('Error: Could not load module ' . $module . '! The file was missing at ' . $file);
			exit();
		}
	}
	
	protected function getChild($child, $args = array()) {
		$action = new Action($child, $args);
		$file = $action->getFile();
		$class = $action->getClass();
		$class_path = $action->getClassPath();
		$method = $action->getMethod();
	
		if (file_exists($file)) 
{
			_require_once($file);

			$controller = new $class ($class_path, $this->registry);
			
			$controller->$method($args);
			
			return $controller->output;
		} else {
			trigger_error('Error: Could not load controller ' . $child . '!');
			exit();
		}
	}
	
	protected function render()
	{
		$this->plugin_handler->call_controller_adapter($this);
		
		//Build Errors
		$this->data['errors'] = array();
		if ($this->error) {
			foreach ($this->error as $e=>$msg) {
				$this->data['errors'][$e] = $msg;
			}
		}
		
		//Build language
		$this->data += $this->language->data;
		
		//Render Children
		foreach ($this->children as $child) {
			$this->data[basename($child)] = $this->getChild($child);
		}
		

		//Render View
		$file = $this->template->get_file();
		
		if (!$file) {
			$this->template->load($this->class_path);
			$file = $this->template->get_file();
		}
		
		//if there are plugins that have modified this template,
		//we use the merged version of this file
		$file = $this->plugin_handler->get_file($file);
		
		//extract the data so it is accessible in the template file
		extract($this->data);
		
		//render the file
		ob_start();
		
  		require($file);
		
  		$this->output = ob_get_contents();
			
		ob_end_clean();
		
		return $this->output;
	}
}