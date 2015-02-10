<?php

/**
 * Title: Braintree Settings
 * Icon: admin.png
 *
 */
class App_Controller_Admin_Settings_Braintree extends Controller
{
	public function index()
	{
		$settings = $_POST;

		if (!IS_POST) {
			$settings = (array)option('braintree_settings');
		}

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

		//Template Data
		$settings['data_order_statuses']  = Order::$statuses;
		$settings['data_braintree_plans'] = $this->System_Extension_Payment_Braintree->getPlans();

		$settings['data_modes'] = array(
			'development' => _l("Development"),
			'sandbox'     => _l("Test Mode"),
			'production'  => _l("Live Mode"),
		);

		//Render
		output($this->render('settings/braintree', $settings));
	}


	public function save()
	{
		if (save_option('braintree_settings', $_POST)) {
			message('success', _l("Braintree settings saved"));
		} else {
			message('error', $this->config->getError());
		}

		if ($this->is_ajax) {
			output_message();
		} elseif ($this->message->has('error')) {
			post_redirect('admin/settings/braintree');
		} else {
			redirect('admin/settings');
		}
	}
}
