<?php
class Admin_Controller_Extension_Payment_BrainTree extends Controller
{
	public function settings(&$settings)
	{
		//Language
		$this->language->load('extension/payment/braintree');

		//Default Settings
		$defaults = array(
			'merchant_id' => '',
			'public_key' => '',
			'private_key' => '',
			'client_side_encryption_key' => '',
			'mode' => 'sandbox',
			'plan_id' => '',
		);

		$settings += $defaults;

		$this->data['settings'] = $settings;

		//Additional Data
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();
		$this->data['data_braintree_plans'] = $this->System_Extension_Payment->get('braintree')->getPlans();

		//Template
		$this->template->load('extension/payment/braintree');

		//Render
		$this->render();
	}

	public function validate()
	{
		return $this->error ? false : true;
	}
}
