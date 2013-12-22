<?php
class Catalog_Controller_Block_Checkout_CustomerInformation extends Controller
{
	public function index()
	{
		$this->template->load('block/checkout/customer_information');
		$this->language->load('block/checkout/customer_information');

		if (!$this->customer->isLogged()) {
			$this->data['guest_checkout'] = true;

			$this->data['block_guest_information'] = $this->getBlock('checkout/guest_information');
		} else {
			//Use Customer Payment Preference
			if ($this->customer->getMeta('default_payment_method_id')) {
				if (!$this->cart->hasPaymentMethod()) {
					$this->cart->setPaymentMethod($this->customer->getMeta('default_payment_method_id'));
				}

				if (!$this->cart->hasPaymentAddress()) {
					$this->cart->setPaymentAddress($this->customer->getMeta('default_payment_address_id'));
				}
			}

			//Load pyament block
			$this->data['block_payment_address'] = $this->getBlock('checkout/payment_address');


			if ($this->cart->hasShipping()) {
				//Use customer Shipping Preference
				if ($this->customer->getMeta('default_shipping_method_id')) {
					if (!$this->cart->hasShippingMethod()) {
						$this->cart->setShippingMethod($this->customer->getMeta('default_shipping_method_id'));
					}

					if (!$this->cart->hasShippingAddress()) {
						$this->cart->setShippingAddress($this->customer->getDefaultShippingAddressId());
					}
				}

				//Load Shipping Block
				$this->data['block_shipping_address'] = $this->getBlock('checkout/shipping_address');
			}
		}

		if ($this->cart->hasShipping()) {
			$this->data['block_shipping_method'] = $this->getBlock('checkout/shipping_method');
		}

		$this->data['block_payment_method'] = $this->getBlock('checkout/payment_method');

		$this->data['validate_customer_checkout'] = $this->url->link('block/checkout/customer_information/validate');

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		$json = array();

		$this->language->load('block/checkout/customer_information');

		if (!$this->cart->hasPaymentAddress()) {
			$json['error']['payment_address'] = $this->_('error_payment_address');
		}

		if (!$this->cart->hasPaymentMethod()) {
			$json['error']['payment_method'] = $this->_('error_payment_method');
		}

		//TODO: Need to add default_payment_method_id and default_shipping_method_id into customer Library API
		//Save Customer Payment Preferences (for future reference)
		if (!$json && $this->customer->isLogged()) {
			$this->customer->setMeta('default_payment_method_id', $this->cart->getPaymentMethodId());
			$this->customer->setDefaultPaymentAddress($this->cart->getPaymentAddressId());
		}

		//Handle Shipping
		if ($this->cart->hasShipping()) {
			if (!$this->cart->hasShippingAddress()) {
				$json['error']['shipping_address'] = $this->_('error_shipping_address');
			}

			if (!$this->cart->hasShippingMethod()) {
				$json['error']['shipping_method'] = $this->_('error_shipping_method');
			}

			//Save Customer Shipping Preferences (for future reference)
			if (!$json && $this->customer->isLogged()) {
				$this->customer->setMeta('default_shipping_method_id', $this->cart->getShippingMethodId());
				$this->customer->setDefaultShippingAddress($this->cart->getShippingAddressId());
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
