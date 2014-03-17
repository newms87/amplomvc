<?php
/**
 * Name: Sidebar
 */
class Admin_Controller_Block_Module_Sidebar extends Controller
{

	public function settings(&$settings)
	{
		//Template
		$this->view->load('block/module/sidebar_settings');

		//The Data
		$this->data['settings'] = $settings;

		//Template Data
		$this->data['data_attribute_groups'] = array('' => _l(" --- None --- ")) + $this->Model_Catalog_AttributeGroup->getAttributeGroups();

		//Render
		$this->render();
	}

	public function save()
	{
		return $this->error;
	}
}
