<?php
abstract class Plugin_Setup
{
	public function __get($key)
	{
		global $registry;
		return $registry->get($key);
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
