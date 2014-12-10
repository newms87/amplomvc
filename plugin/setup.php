<?php
abstract class Plugin_Setup extends Model
{
	public function install()
	{
		//Installation Code goes here
	}

	public function uninstall($keep_data = true)
	{
		//Uninstall code goes here
	}

	public function upgrade($from_version)
	{
		//Upgrade code goes here
	}
}
