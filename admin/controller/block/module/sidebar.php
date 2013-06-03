<?php
class Admin_Controller_Block_Module_Sidebar extends Controller 
{
	
	public function settings(&$settings)
	{
		$this->template->load('block/module/sidebar_settings');
		
		$this->data['settings'] = $settings;
		
		$this->data['data_attribute_groups'] = array('' => $this->_('text_none')) + $this->Model_Catalog_AttributeGroup->getAttributeGroups();
		
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
