<?php
class Catalog_Controller_Block_Checkout_PaymentAddress extends Controller
{
	public function build()
	{
		$data['data_addresses'] = $this->customer->getPaymentAddresses();

		if ($this->cart->validatePaymentAddress()) {
			$data['payment_address_id'] = $this->cart->getPaymentAddressId();
		} else {
			$data['payment_address_id'] = $this->customer->getMeta('default_payment_address_id');
		}

		//Build Address Form
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_action(site_url('block/checkout/payment_address/validate_form'));
		$this->form->set_field_options('country_id', $this->Model_Localisation_Country->getCountries(), array('country_id' => 'name'));

		$yes_no = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$this->form->set_field_options('default', $yes_no);

		$data['form_payment_address'] = $this->form->build();

		$data['validate_selection'] = site_url('block/checkout/payment_address/validate_selection');

		$this->response->setOutput($this->render('block/checkout/payment_address', $data));
	}

	public function validate_selection()
	{
		$json = $this->validate();

		if (!$json) {
			if (empty($_POST['address_id'])) {
				$json['error']['warning'] = _l("Invalid Billing Address!");
			} else {
				if (!$this->cart->setPaymentAddress($_POST['address_id'])) {
					$json['error']['address'] = $this->cart->getError('payment_address');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function validate_form()
	{
		$json = $this->validate();

		if (!$json) {
			//Validate the form
			$this->form->init('address');

			if (!$this->form->validate($_POST)) {
				$json['error'] = $this->form->getError();
			}

			//Additional Form Validation here...


			if (!$json) {
				if (!$this->cart->setPaymentAddress($_POST)) {
					$json['error']['payment_address'] = $this->cart->getError('payment_address');
				}
			}

			if (!$json) {
				$json['success'] = _l("Your Payment Address has been verified!");
			}

			//If this is not an ajax call
			if (!$this->request->isAjax()) {
				if (!empty($json['success'])) {
					$this->message->add('success', $json['success']);
				} elseif(!empty($json['error'])) {
					$this->message->add('error', $json['error']);
				}

				//We redirect because we are only a block, not a full page!
				redirect('checkout');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function validate()
	{
		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = site_url('checkout');
		} elseif (!$this->cart->validate()) {
			$json['redirect'] = site_url('cart');
			$this->message->add('warning', $this->cart->getError());
		}

		return $json;
	}
}
