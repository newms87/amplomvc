<?php
class Catalog_Controller_Block_Checkout_PaymentMethod extends Controller
{
	public function build()
	{
		if ($this->cart->hasPaymentAddress()) {
			$payment_methods = $this->cart->getPaymentMethods();

			if (!$payment_methods) {
				$payment_methods = array();
			} else {
				if ($this->cart->hasPaymentMethod()) {
					$data['code'] = $this->cart->getPaymentCode();
				} else {
					$data['code'] = key($payment_methods);
				}
			}

			$data['payment_methods'] = $payment_methods;
		} else {
			$data['no_payment_address'] = true;
		}

		if (option('config_checkout_terms_info_id')) {
			$information_info = $this->Model_Catalog_Information->getInformation(option('config_checkout_terms_info_id'));

			if ($information_info) {
				$data['checkout_terms']       = site_url('information/information/info', 'information_id=' . option('config_checkout_terms_info_id'));
				$data['checkout_terms_title'] = $information_info['title'];
			}
		}

		//Session data
		$session_defaults = array(
			'comment' => '',
			'agree'   => '',
		);

		$data += $_SESSION + $session_defaults;

		//Actions
		$data['validate_payment_method'] = site_url('block/checkout/payment_method/validate');

		//Render
		$this->response->setOutput($this->render('block/checkout/payment_method', $data));
	}

	public function validate()
	{
		$json = array();

		// Validate if payment address has been set.
		if ($this->cart->hasPaymentAddress()) {
			$payment_address = $this->cart->getPaymentAddress();
		} else {
			$json['error']['payment_address'] = _l("Invalid Billing Address");
		}

		if (!$this->cart->validate()) {
			$json['redirect'] = site_url('cart/cart');
			$this->message->add('warning', $this->cart->getError());
		}

		if (!$json) {
			if (option('config_checkout_terms_info_id')) {
				$information_info = $this->Model_Catalog_Information->getInformation(option('config_checkout_terms_info_id'));

				if ($information_info && empty($_POST['agree'])) {
					$json['error']['agree'] = _l("You must agree to the %", $information_info['title']);
				}
			}

			if (!isset($_POST['payment_method'])) {
				//_payment_method to avoid $this->builder->js('errors', ...) adding message on form
				$json['error']['_payment_method'] = _l("Invalid Payment Method");
			} elseif (!$this->cart->setPaymentMethod($_POST['payment_method'])) {
				$json['error'] = $this->cart->getError('payment_method');
			}

			if (!$json) {
				$this->cart->setComment($_POST['comment']);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
