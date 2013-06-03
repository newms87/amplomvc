<?php
class _Setup implements PluginSetup 
{
	function install()
	{
		trigger_error("This is not implemented!");
		exit;
		$hooks = array(
			'settings_validate' => array('when'=>'after', 'callback'=>'validate')
		);
		
		$plugins[] = array(
			'plugin_path'  =>'admin/cms',
			'base_type'	=>'admin',
			'type'			=>'controller',
			'class_path'	=>'setting/setting',
			'route'		=>'setting/setting',
			'hooks'		=>$hooks,
			'on_render'	=>'settings',
			'status'		=>1,
		);
		
		$file_modifications = array(
			'admin/view/template/setting/setting.tpl'=>'view/setting.tpl'
		);
	}
	
	function update($version)
	{
		switch($version){
			case '1.53':
			case '1.52':
			case '1.51':
			default:
				break;
		}
	}
	
	function uninstall()
	{
	}
}