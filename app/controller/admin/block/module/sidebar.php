<?php
/**
 * Name: Sidebar
 */
class App_Controller_Admin_Block_Module_Sidebar extends Controller
{

	public function settings(&$settings)
	{
		//The Data
		$data['settings'] = $settings;

		//Template Data
		$data['data_attribute_groups'] = array('' => _l(" --- None --- ")) + $this->Model_Catalog_AttributeGroup->getAttributeGroups();

		//Render
		$this->render('block/module/sidebar_settings', $data);
	}

	public function save()
	{
		return $this->error;
	}
}
