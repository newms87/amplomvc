<?php
class Catalog_Controller_Block_Checkout_GuestInformation extends Controller
{
	public function build()
	{
		//Extra Information saved about the guest (name, email, etc..)
		$guest_info = $this->cart->loadGuestInfo();

		$this->form->init('register');
		$this->form->set_template('form/single_column');
		$this->form->show_form_tag(false);
		$this->form->set_fields('firstname', 'lastname', 'email');
		$this->form->set_data($guest_info);

		$data['form_guest_info'] = $this->form->build();

		$this->form->init('address');
		$this->form->set_template('form/single_column');
		$this->form->show_form_tag(false);
		$this->form->set_field_options('country_id', $this->Model_Localisation_Country->getCountries(), array('country_id' => 'name'));
		$this->form->disable_fields('firstname', 'lastname', 'default', 'submit_address');
		$this->form->set_name_format('payment_address[%name%]');

		if ($this->cart->hasPaymentAddress()) {
			$this->form->set_data($this->Cart->getPaymentAddress());
		}

		$data['form_payment_address'] = $this->form->build();

		//Shipping
		if ($this->cart->hasShipping()) {
			$this->form->enable_fields('firstname', 'lastname');
			$this->form->set_name_format('shipping_address[%name%]');

			if ($this->cart->hasShippingAddress()) {
				$this->form->set_data($this->cart->getShippingAddress());
			}

			$data['form_shipping_address'] = $this->form->build();

			$data['same_shipping_address'] = isset($guest_info['same_shipping_address']) ? $guest_info['same_shipping_address'] : 1;
		}

		$data['validate_guest_checkout'] = site_url('block/checkout/guest_information/validate');

		$this->response->setOutput($this->render('block/checkout/guest_information', $data));
	}

	public function validate()
	{
		$json = array();

		if ($this->customer->isLogged()) {
			$json['redirect'] = site_url('checkout/checkout');
		} elseif ((!$this->cart->hasProducts() && !$this->cart->hasVouchers()) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = site_url('cart/cart');
		} elseif (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
			$json['redirect'] = site_url('cart/cart');
		}

		//Redirect if set
		if ($json) {
			if ($this->request->isAjax()) {
				redirect('checkout/checkout');
			}
		} else {
			//Validate Guest Information
			$this->form->init('register');
			$this->form->set_fields('firstname', 'lastname', 'email');

			if (!$this->form->validate($_POST)) {
				$json['error'] = $this->form->getError();
			}

			//Save Guest Information
			if (!$json) {
				$this->cart->saveGuestInfo($_POST);
			}

			//Validate Payment Address
			$this->form->init('address');
			$this->form->set_name_format('payment_address[%name%]');

			$_POST['payment_address']['firstname'] = $_POST['firstname'];
			$_POST['payment_address']['lastname']  = $_POST['lastname'];

			if (!$this->form->validate($_POST['payment_address'])) {
				if (!isset($json['error'])) {
					$json['error'] = array();
				}

				$json['error'] += $this->form->getError();
			}

			if (!$json) {
				if (!$this->cart->setPaymentAddress($_POST['payment_address'])) {
					$json['error']['payment_address'] = $this->cart->getError('payment_address');
				}
			}

			//Same Shipping as Billing
			if (!empty($_POST['same_shipping_address'])) {
				if (!$this->cart->setShippingAddress($this->cart->getPaymentAddressId())) {
					$this->error['shipping_address'] = $this->cart->getError('shipping_address');
				}
			} else {
				//Validate Shipping Address
				$this->form->set_name_format('shipping_address[%name%]');

				if (!$this->form->validate($_POST['shipping_address'])) {
					if (!isset($json['error'])) {
						$json['error'] = array();
					}

					$json['error'] += $this->form->getError();
				}

				if (!$json) {
					if (!$this->cart->setShippingAddress($_POST['shipping_address'])) {
						$json['error']['shipping_address'] = $this->cart->getError('shipping_address');
					}
				}
			}

			//If this is an ajax request
			if (!$this->request->isAjax()) {
				if ($json['error']) {
					$this->message->add('warning', $json['error']);
				} else {
					$this->message->add('success', _l("You have successfully added an address"));
				}

				//We redirect because we are only a block, not a full page!
				redirect('checkout/checkout');
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
