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

		$data['settings'] = $settings;

		//Template Data
		$data['data_tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();

		//Render
		$this->render('extension/total/handling', $data);
	}
}
