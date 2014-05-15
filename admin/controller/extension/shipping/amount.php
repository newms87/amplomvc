<?php

class Admin_Controller_Extension_Shipping_Amount extends Controller
{
	public function settings(&$settings)
	{
		//Default Settings
		$defaults = array(
			'priceset' => array(),
			'zonerule' => array(),
		);

		$settings += $defaults;

		if (!is_array($settings['priceset'])) {
			$settings['priceset'] = array();
		}

		if (!is_array($settings['zonerule'])) {
			$settings['zonerule'] = array();
		}

		$data = $settings;

		//AC Templates
		$data['priceset']['__ac_template__'] = array(
			'range' => 'gt',
			'total' => 0,
			'cost'  => 10.00,
			'type'  => 'fixed',
			'from'  => 0,
			'to'    => 100,
		);

		$data['zonerule']['__ac_template__'] = array(
			'country_id' => 223,
			'zone_id'    => 0,
			'mod'        => 'add',
			'cost'       => 10.00,
			'type'       => 'fixed',
		);

		//Template Data
		$data['data_types'] = array(
			'percent' => _l("Percent"),
			'fixed'   => _l("Fixed Amount")
		);

		$data['data_ranges'] = array(
			'lt'    => _l('Less than'),
			'lte'   => _l('Less than or equals'),
			'gt'    => _l('Greater than'),
			'gte'   => _l('Greater than or equals'),
			'eq'    => _l('Equals'),
			'range' => _l('range'),
		);

		$data['data_mods'] = array(
			'add'      => "+",
			'subtract' => '-',
			'fixed'    => "="
		);

		$data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		//Render
		$this->render('extension/shipping/amount', $data);
	}
}
