<?php
class Admin_Controller_Extension_Payment_BrainTree extends Controller
{
	public function settings(&$settings)
	{
		//Default Settings
		$defaults = array(
			'merchant_id'                => '',
			'public_key'                 => '',
			'private_key'                => '',
			'client_side_encryption_key' => '',
			'mode'                       => 'sandbox',
			'plan_id'                    => '',
		);

		$settings += $defaults;

		$data['settings'] = $settings;

		//Template Data
		$data['data_order_statuses']  = $this->order->getOrderStatuses();
		$data['data_braintree_plans'] = $this->System_Extension_Payment_Braintree->getPlans();

		$data['data_modes'] = array(
			'sandbox' => _l("Test Mode"),
			'live'    => _l("Live Mode"),
		);

		//Render
		$this->render('extension/payment/braintree', $data);
	}

	public function validate()
	{
		return $this->error ? false : true;
	}
}
