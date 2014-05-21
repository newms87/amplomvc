<?php
class App_Controller_Block_Account_Card extends Controller
{
	public function select($settings = array())
	{
		$settings['payment_code'] = 'braintree';

		//Entry Data
		$settings['cards'] = $this->System_Extension_Payment->get($settings['payment_code'])->getCards();

		if (!isset($settings['payment_key'])) {
			$settings['payment_key'] = $this->customer->getDefaultPaymentMethod($settings['payment_code']);
		}

		//Action Buttons
		$settings['register_card'] = site_url('block/account/card/register');

		//Render
		return $this->render('block/account/card/select', $settings);
	}

	public function register($settings = array()) {
		$this->render('block/account/card/register', $settings);
	}
}
