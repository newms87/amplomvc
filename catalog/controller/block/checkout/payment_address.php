<?php
class Catalog_Controller_Block_Checkout_PaymentAddress extends Controller
{
	public function index()
	{
		$this->template->load('block/checkout/payment_address');
		$this->data['data_addresses'] = $this->customer->getPaymentAddresses();

		if ($this->cart->validatePaymentAddress()) {
			$this->data['payment_address_id'] = $this->cart->getPaymentAddressId();
		} else {
			$this->data['payment_address_id'] = $this->customer->getMeta('default_payment_address_id');
		}

		//Build Address Form
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_action($this->url->link('block/checkout/payment_address/validate_form'));
		$this->form->set_field_options('country_id', $this->Model_Localisation_Country->getCountries(), array('country_id' => 'name'));

		$yes_no = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$this->form->set_field_options('default', $yes_no);

		$this->data['form_payment_address'] = $this->form->build();

		$this->data['validate_selection'] = $this->url->link('block/checkout/payment_address/validate_selection');

		$this->response->setOutput($this->render());
	}

	public function validate_selection()
	{
		$json = $this->validate();

		if (!$json) {
			if (empty($_POST['address_id'])) {
				$json['error']['warning'] = _l("Invalid Billing Address!");
			} else {
				if (!$this->cart->setPaymentAddress($_POST['address_id'])) {
					$json['error']['address'] = $this->cart->get_errors('payment_address');
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
				$json['error'] = $this->form->get_errors();
			}

			//Additional Form Validation here...


			if (!$json) {
				if (!$this->cart->setPaymentAddress($_POST)) {
					$json['error']['payment_address'] = $this->cart->get_errors('payment_address');
				}
			}

			//IF this is an ajax call
			if (!isset($_POST['async'])) {
				if ($json['error']) {
					$this->message->add('warning', $json['error']);
				} else {
					$this->message->add('success', _l("Your payment address has been verified!"));
				}

				//We redirect because we are only a block, not a full page!
				$this->url->redirect('checkout/checkout');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function validate()
	{
		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
		} elseif (!$this->cart->validate()) {
			$json['redirect'] = $this->url->link('cart/cart');
			$this->message->add('warning', $this->cart->get_errors());
		}

		return $json;
	}
}
