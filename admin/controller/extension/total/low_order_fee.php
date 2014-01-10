<?php
class Admin_Controller_Extension_Total_LowOrderFee extends Controller
{
	public function settings(&$settings)
	{
		//Language
		$this->language->load('extension/total/low_order_fee');

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
		$this->template->load('extension/total/low_order_fee');

		//Render
		$this->render();
	}
}
