<?php
class Catalog_Controller_Block_Checkout_CustomerInformation extends Controller
{
	public function build()
	{
		if (!$this->customer->isLogged()) {
			$data['guest_checkout'] = true;

			$data['block_guest_information'] = $this->block->render('checkout/guest_information');
		} else {
			//Use Customer Payment Preference
			if ($this->customer->getMeta('default_payment_code')) {

				if (!$this->cart->hasPaymentMethod()) {
					$payment_method = $this->customer->getDefaultPaymentMethod(option('config_default_payment_code', 'braintree'));
					if ($payment_method) {
						$this->cart->setPaymentMethod($payment_method['payment_code'], $payment_method['payment_key']);
					}
				}

				if (!$this->cart->hasPaymentAddress()) {
					$this->cart->setPaymentAddress($this->customer->getMeta('default_payment_address_id'));
				}
			}

			//Load payment block
			$data['block_payment_address'] = $this->block->render('checkout/payment_address');


			if ($this->cart->hasShipping()) {
				//Use customer Shipping Preference
				if ($this->customer->getMeta('default_shipping_code')) {
					if (!$this->cart->hasShippingMethod()) {
						$shipping_method = $this->customer->getDefaultShippingMethod(option('config_default_shipping_code', 'flat'));
						if ($shipping_method) {
							$this->cart->setShippingMethod($shipping_method['shipping_code'], $shipping_method['shipping_key']);
						}
					}

					if (!$this->cart->hasShippingAddress()) {
						$this->cart->setShippingAddress($this->customer->getDefaultShippingAddressId());
					}
				}

				//Load Shipping Block
				$data['block_shipping_address'] = $this->block->render('checkout/shipping_address');
			}
		}

		if ($this->cart->hasShipping()) {
			$data['block_shipping_method'] = $this->block->render('checkout/shipping_method');
		}

		$data['block_payment_method'] = $this->block->render('checkout/payment_method');

		$data['validate_customer_checkout'] = site_url('block/checkout/customer_information/validate');

		//Render
		$this->response->setOutput($this->render('block/checkout/customer_information', $data));
	}

	public function validate()
	{
		$json = array();

		if (!$this->cart->hasPaymentAddress()) {
			$json['error']['payment_address'] = _l("Please provide a Payment Address.");
		}

		if (!$this->cart->hasPaymentMethod()) {
			$json['error']['payment_method'] = _l("Please specify a Payment Method");
		}

		//Save Customer Payment Preferences (for future reference)
		if (!$json && $this->customer->isLogged()) {
			$this->customer->setDefaultPaymentMethod($this->cart->getPaymentCode(), $this->cart->getPaymentKey());
			$this->customer->setDefaultPaymentAddress($this->cart->getPaymentAddressId());
		}

		//Handle Shipping
		if ($this->cart->hasShipping()) {
			if (!$this->cart->hasShippingAddress()) {
				$json['error']['shipping_address'] = _l("Please provide a Shipping Address.");
			}

			if (!$this->cart->hasShippingMethod()) {
				$json['error']['shipping_method'] = _l("Please specify a Shipping Method");
			}

			//Save Customer Shipping Preferences (for future reference)
			if (!$json && $this->customer->isLogged()) {
				$this->customer->setDefaultShippingMethod($this->cart->getShippingCode(), $this->cart->getShippingKey());
				$this->customer->setDefaultShippingAddress($this->cart->getShippingAddressId());
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
