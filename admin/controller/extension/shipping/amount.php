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

		$this->data = $settings;

		//AC Templates
		$this->data['priceset']['__ac_template__'] = array(
			'range' => 'gt',
			'total' => 0,
			'cost'  => 10.00,
			'type'  => 'fixed',
			'from'  => 0,
			'to'    => 100,
		);

		$this->data['zonerule']['__ac_template__'] = array(
			'country_id' => 223,
			'zone_id'    => 0,
			'mod'        => 'add',
			'cost'       => 10.00,
			'type'       => 'fixed',
		);

		//Template Data
		$this->data['data_types'] = array(
			'percent' => _l("Percent"),
			'fixed'   => _l("Fixed Amount")
		);

		$this->data['data_ranges'] = array(
			'lt'    => "<",
			'lte'   => '<=',
			'gt'    => ">",
			'gte'   => '>=',
			'eq'    => '=',
			'range' => _l('range'),
		);

		$this->data['data_mods'] = array(
			'add'      => "+",
			'subtract' => '-',
			'fixed'    => "="
		);

		$this->data['data_countries'] = $this->Model_Localisation_Country->getCountries();

		//Template
		$this->template->load('extension/shipping/amount');

		//Render
		$this->render();
	}
}
