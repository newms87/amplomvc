<?php
class Admin_Controller_Extension_Shipping_Amount extends Controller
{
	public function settings(&$settings)
	{
		//Default Settings
		$defaults = array(
			'amount_priceset' => array(),
			'amount_zonerule' => array(),
		);

		$settings += $defaults;

		if (!is_array($settings['amount_priceset'])) {
			$settings['amount_priceset'] = array();
		}

		if (!is_array($settings['amount_zonerule'])) {
			$settings['amount_zonerule'] = array();
		}

		$this->data['settings'] = $settings;

		//Additional Data
		$this->data['priceset_types'] = array(
			'percent' => _l("Percent"),
			'fixed'   => _l("Fixed Amount")
		);

		$this->data['priceset_ranges'] = array(
			'lt'    => "<",
			'lte'   => '<=',
			'gt'    => ">",
			'gte'   => '>=',
			'eq'    => '=',
			'range' => _l('range'),
		);

		$this->data['rule_mods'] = array(
			'add'      => "+",
			'subtract' => '-',
			'fixed'    => "="
		);

		//Template
		$this->template->load('extension/shipping/amount');

		//Render
		$this->render();
	}
}
