<?php
abstract class PluginSetup
{
	private $registry;
	
	function __construct($registry)
	{
		$this->registry = $registry;
	}
	
	public function __get($key)
	{
		return $this->registry->get($key);
	}
	
	public function install()
	{
		//Installation Code goes here
	}
	
	public function uninstall($keep_data = true)
	{
		//Uninstall code goes here
	}
	
	public function update($version)
	{
		//Update code goes here
	}
}