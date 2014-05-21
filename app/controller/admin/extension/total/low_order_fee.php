<?php
class App_Controller_Admin_Extension_Total_LowOrderFee extends Controller
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
		$this->render('extension/total/low_order_fee', $data);
	}
}
