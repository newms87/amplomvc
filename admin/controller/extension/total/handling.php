<?php
class Admin_Controller_Extension_Total_Handling extends Controller
{
	public function settings(&$settings)
	{
		//Default Settings
		$defaults = array(
			'total'        => '',
			'fee'          => '',
			'tax_class_id' => '',
		);

		$settings += $defaults;

		$this->data['settings'] = $settings;

		//Template Data
		$this->data['data_tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();

		//The Template
		$this->view->load('extension/total/handling');

		//Render
		$this->render();
	}
}
