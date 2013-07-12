<?php

abstract class Controller 
{
	protected $registry;
	protected $children = array();
	public $output;
	public $template;
	public $data = array();
	public $error = array();
	
	public function __construct($registry)
	{
		$this->registry = $registry;
		
		$this->template = new Template($registry);
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
		if (func_num_args() > 1) {
			$args = func_get_args();
			
			return call_user_func_array(array($this->language, 'format'), $args);
		}
		
		return $this->language->get($key);
	}

	//TODO: move this to block plugin!
	protected function getBlock($path, $args = array())
	{
		$block = 'block/' . $path;
		
		if (!is_array($args)) {
			trigger_error('Error: In Controller ' . get_class($this) . ' while retreiving block ' . $block . ' - $args passed to Controller::getBlock() must be an array of parameters to be passed to the block method. ' . get_caller());
			exit();
		}
		
		$block_settings = $this->Model_Block_Block->getBlockSettings($path);
		
		$settings = $args;
		
		if (!empty($block_settings)) {
			$settings += $block_settings;
		}
		
		$action = new Action($this->registry, $block, array('settings' => $settings));
		
		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error('Error: Could not load block ' . $block . '! The file was missing.');
		}
	}
	
	protected function getChild($child, $parameters = array()) {
		$action = new Action($this->registry, $child, $parameters);
		
		if ($action->execute()) {
			return $action->getOutput();
		} else {
			trigger_error('Could not load controller ' . $child . '!');
			exit();
		}
	}
	
	protected function render()
	{
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
		
		$this->template->setData($this->data);
		
		$this->output = $this->template->render();
		
		return $this->output;
	}
}