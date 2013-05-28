<?php
class SetupCms implements SetupPlugin {
	function install($registry, &$controller_adapters, &$db_requests){
	
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
	
	function update($version, $registry){
		switch($version){
			case '1.53':
			case '1.52':
			case '1.51':
			default:
				break;
		}
	}
	
	function uninstall($registry){
	}
}