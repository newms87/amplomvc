<?php
class Catalog_Controller_Block_Checkout_ShippingMethod extends Controller
{
	public function index()
	{
		$this->template->load('block/checkout/shipping_method');

		if (isset($_POST['shipping_method'])) {
			$this->validate();
		}

		if ($this->cart->hasShippingAddress()) {
			$shipping_methods = $this->cart->getShippingMethods();

			if (!empty($shipping_methods)) {
				$this->data['shipping_methods'] = $shipping_methods;

				$shipping_method_id = '';

				if ($this->cart->hasShippingMethod()) {
					$shipping_method_id = $this->cart->getShippingMethodId();
				} else {
					//Check the first shipping method, if not selected
					$shipping_method_id = key($shipping_methods);
				}

				$this->data['shipping_method_id'] = $shipping_method_id;
			} else {
				$this->data['cart_error_shipping_method'] = $this->cart->get_errors('shipping_method');
				$this->data['allowed_shipping_zones']     = $this->cart->getAllowedShippingZones();
			}
		} else {
			$this->data['no_shipping_address'] = true;
		}

		$this->data['validate_shipping_method'] = $this->url->link('block/checkout/shipping_method/validate');

		$this->response->setOutput($this->render());
	}

	public function validate()
	{
		$json = array();

		// Validate cart contents
		if (!$this->cart->validate()) {
			$this->message->add('warning', $this->cart->get_errors());
			$json['redirect'] = $this->url->link('cart/cart');
		} elseif (!$this->cart->hasShipping()) {
			$this->message->add('warning', _l("Shipping is not required for this order."));
			$json['redirect'] = $this->url->link('checkout/checkout');
		}

		if (!$json) {
			if (!isset($_POST['shipping_method'])) {
				$json['error']['warning'] = _l("Invalid Delivery Method");
			} else {
				if (!$this->cart->setShippingMethod($_POST['shipping_method'])) {
					$json['error']['shipping_method'] = $this->cart->get_errors('shipping_method');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
