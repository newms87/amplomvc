<?php
class Catalog_Controller_Block_Checkout_ShippingMethod extends Controller
{
	public function build()
	{
		if (isset($_POST['shipping_method'])) {
			$this->validate();
		}

		if ($this->cart->hasShippingAddress()) {
			$shipping_methods = $this->cart->getShippingMethods();

			if (!empty($shipping_methods)) {
				$shipping_code = $this->cart->getShippingCode();
				$shipping_key = $this->cart->getShippingKey();

				foreach ($shipping_methods as $code => &$method) {
					$method['quotes'] = $this->System_Extension_Shipping->get($code)->getQuotes($this->cart->getShippingAddress());

					if ($shipping_code === $code && isset($method['quotes'][$shipping_key])) {
						$method['quotes'][$shipping_key]['selected'] = true;
					}
				}
				unset($method);

				$data['shipping_methods'] = $shipping_methods;
			} else {
				$data['cart_error_shipping_method'] = $this->cart->getError('shipping_method');
				$data['allowed_shipping_zones']     = $this->cart->getAllowedShippingZones();
			}
		} else {
			$data['no_shipping_address'] = true;
		}

		$data['validate_shipping_method'] = site_url('block/checkout/shipping_method/validate');

		//Render
		$this->response->setOutput($this->render('block/checkout/shipping_method', $data));
	}

	public function validate()
	{
		$json = array();

		// Validate cart contents
		if (!$this->cart->validate()) {
			$this->message->add('warning', $this->cart->getError());
			$json['redirect'] = site_url('cart');
		} elseif (!$this->cart->hasShipping()) {
			$this->message->add('warning', _l("Shipping is not required for this order."));
			$json['redirect'] = site_url('checkout/checkout');
		}

		if (!empty($_POST['shipping_method']) && strpos($_POST['shipping_method'], ',') !== false) {
			list ($shipping_code, $shipping_key) = explode(',', $_POST['shipping_method']);
		} else {
			$json['error']['warning'] = _l("Please select a shipping method.");
		}

		if (!$json) {
			if (!$shipping_code) {
				$json['error']['warning'] = _l("Invalid Delivery Method");
			} else {
				if (!$this->cart->setShippingMethod($shipping_code, $shipping_key)) {
					$json['error']['warning'] = $this->cart->getError('shipping_method');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
