<?php
class ControllerBlockModuleSidebar extends Controller 
{
	
	public function settings(&$settings)
	{
		$this->template->load('block/module/sidebar_settings');
		
		$this->data['settings'] = $settings;
		
		$this->data['data_attribute_groups'] = array('' => $this->_('text_none')) + $this->model_catalog_attribute_group->getAttributeGroups();
		
		$this->render();
	}
	
	/*
	public function profile(&$profiles)
	{
		$this->template->load('block/module/sidebar_profile');

		$this->data['profiles'] += $profiles;
		
		//Add your code here
		
		$this->render();
	}
	*/
	
	public function validate()
	{
		return $this->error;
	}
}
