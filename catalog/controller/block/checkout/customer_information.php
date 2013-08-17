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
			$payment_preference = $this->customer->get_setting('payment_preference');

			if ($payment_preference) {
				if (!$this->cart->hasPaymentMethod()) {
					$this->cart->setPaymentMethod($payment_preference['method']);
				}

				if (!$this->cart->hasPaymentAddress()) {
					$this->cart->setPaymentAddress($payment_preference['address']);
				}
			}

			//Load pyament block
			$this->data['block_payment_address'] = $this->getBlock('checkout/payment_address');


			if ($this->cart->hasShipping()) {
				//Use customer Shipping Preference
				$shipping_preference = $this->customer->get_setting('shipping_preference');

				if ($shipping_preference) {
					if (!$this->cart->hasShippingMethod()) {
						$this->cart->setShippingMethod($shipping_preference['method']);
					}

					if (!$this->cart->hasShippingAddress()) {
						$this->cart->setShippingAddress($shipping_preference['address']);
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

		//Save Customer Payment Preferences (for future reference)
		if (!$json && $this->customer->isLogged()) {
			$payment_preference = array(
				'method'  => $this->cart->getPaymentMethodId(),
				'address' => $this->cart->getPaymentAddressId(),
			);

			$this->customer->set_setting('payment_preference', $payment_preference);
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
				$shipping_preference = array(
					'method'  => $this->cart->getShippingMethodId(),
					'address' => $this->cart->getShippingAddressId(),
				);

				$this->customer->set_setting('shipping_preference', $shipping_preference);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}