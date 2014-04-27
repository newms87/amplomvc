<?php
class Catalog_Controller_Block_Checkout_ConfirmAddress extends Controller
{
	public function build($settings = array())
	{
		if ($this->cart->hasShipping() && $this->cart->hasShippingAddress()) {
			$shipping_address = $this->cart->getShippingAddress();

			//Format Shipping Addresses
			if ($shipping_address) {
				$data['shipping_address'] = $this->address->format($shipping_address);
			}
		}

		if ($this->cart->hasPaymentAddress()) {
			$payment_address = $this->cart->getPaymentAddress();

			if ($payment_address) {
				$data['payment_address'] = $this->address->format($payment_address);
			}
		}

		$this->response->setOutput($this->render('block/checkout/confirm_address', $data));
	}

	public function string_to_html($format)
	{
		return preg_replace("/<br[^>]*>\s*<br[^>]*>/", "<br>", nl2br($format));
	}
}
