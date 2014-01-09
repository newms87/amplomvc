<?php
class Admin_Controller_Extension_Payment_PpStandard extends Controller
{
	public function settings(&$settings)
	{
		//Language
		$this->language->load('extension/payment/pp_standard');

		//Default Settings
		$defaults = array(
			'button_graphic'              => 'https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif',
			'email'                       => '',
			'test_email'                  => '',
			'test'                        => '',
			'transaction'                 => '',
			'debug'                       => '',
			'canceled_reversal_status_id' => '',
			'denied_status_id'            => '',
			'expired_status_id'           => '',
			'failed_status_id'            => '',
			'pending_status_id'           => '',
			'processed_status_id'         => '',
			'refunded_status_id'          => '',
			'reversed_status_id'          => '',
			'voided_status_id'            => '',
			'geo_zone_id'                 => '',
			'sort_order'                  => '',
			'page_style'                  => '',
			'pdt_enabled'                 => false,
			'pdt_token'                   => '',
			'auto_return_url'             => '',
		);

		$settings += $defaults;

		$this->data['settings'] = $settings;

		//Additional Data
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();

		$_['data_auth_sale'] = array(
			0 => _l("Authorization"),
			1 => _l("Sale"),
		);

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Template
		$this->template->load('extension/payment/pp_standard');

		//Render
		$this->render();
	}

	public function validate()
	{
		$settings = $_POST['settings'];

		if (!$this->validation->email($settings['email'])) {
			$this->error['settings[email]'] = $this->_('error_email');
		}

		if (!empty($settings['test_email']) && !$this->validation->email($settings['test_email'])) {
			$this->error['settings[test_email]'] = $this->_('error_test_email');
		}

		if ($settings['pdt_enabled'] && !$settings['pdt_token']) {
			$this->error['settings[pdt_token]'] = $this->_('error_pdt_token');
		}

		return $this->error ? false : true;
	}
}
