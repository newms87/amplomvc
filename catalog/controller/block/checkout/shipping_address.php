<?php
class Catalog_Controller_Block_Checkout_ShippingAddress extends Controller
{
	public function index()
	{
		$this->language->load('checkout/checkout');
		$this->template->load('block/checkout/shipping_address');

		if ($this->cart->validateShippingAddress()) {
			$this->data['shipping_address_id'] = $this->cart->getShippingAddressId();
		} else {
			$this->data['shipping_address_id'] = $this->customer->getDefaultShippingAddressId();
		}

		$this->data['data_addresses'] = $this->customer->getShippingAddresses();

		//Build Address Form
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_action($this->url->link('block/checkout/shipping_address/validate_form'));
		$this->form->set_field_options('country_id', $this->Model_Localisation_Country->getCountries(), array('country_id' => 'name'));

		$yes_no = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$this->form->set_field_options('default', $yes_no);

		$this->data['form_shipping_address'] = $this->form->build();

		$this->data['validate_selection'] = $this->url->link('block/checkout/shipping_address/validate_selection');

		$this->response->setOutput($this->render());
	}

	public function validate_selection()
	{
		$this->language->load('checkout/checkout');

		$json = $this->validate();

		if (!$json) {
			if (empty($_POST['address_id'])) {
				$json['error']['warning'] = _l("Invalid Delivery Address");
			} else {
				if (!$this->cart->setShippingAddress($_POST['address_id'])) {
					$json['error']['address'] = $this->cart->get_errors('shipping_address');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function validate_form()
	{
		$this->language->load('checkout/checkout');

		$json = $this->validate();

		if (!$json) {
			//Validate Shipping Address Form
			$this->form->init('address');

			if (!$this->form->validate($_POST)) {
				$json['error'] = $this->form->get_errors();
			}

			if (!$json && !$this->cart->validateShippingAddress($_POST)) {
				$json['error']['shipping_address'] = $this->cart->get_errors('shipping_address');
			} else {
				if (!$this->cart->setShippingAddress($_POST)) {
					$json['error']['shipping_address'] = $this->cart->get_errors('shipping_address');
				}
			}

			//If this is not an ajax call
			if (!isset($_POST['async'])) {
				if ($json) {
					$this->message->add('warning', $json['error']);
				} else {
					$this->message->add('success', _l("Delivery address is valid!"));
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
			$this->message->add($this->cart->get_errors());
		} elseif (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout');
			$this->message->add('warning', _l("Shipping is not required for this order"));
		}

		return $json;
	}
}
