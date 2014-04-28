<?php
class Catalog_Controller_Block_Checkout_Confirm extends Controller
{
	public function build()
	{
		//Verify the shipping details, if only the shipping method is invalid, choose a shipping method automatically
		if (!$this->cart->validateShippingMethod()) {
			if ($this->cart->hasShippingAddress()) {
				$methods = $this->cart->getShippingMethods();

				if (!empty($methods)) {
					$this->cart->setShippingMethod(current($methods));
				} else {
					$data['redirect'] = site_url('checkout/checkout');
					$this->message->add('warning', $this->cart->getError());
				}
			}
		}

		//Verify the payment details, if only the payment method is invalid, choose a payment method automatically
		if (!$this->cart->validatePaymentMethod()) {
			if ($this->cart->hasPaymentAddress() && !$this->cart->hasPaymentMethod()) {
				$methods = $this->cart->getPaymentMethods();

				if (!empty($methods)) {
					$method = current($methods);
					$this->cart->setPaymentMethod($method['code']);
				} else {
					$data['redirect'] = site_url('checkout/checkout');
					$this->message->add('warning', $this->cart->getError());
				}
			}
		}

		if (empty($data['redirect'])) {
			if (!$this->cart->validateCheckout()) {
				$this->message->add('warning', $this->cart->getError());

				//If the cart contents are invalid (ie: out of stock), redirect to cart
				if ($this->cart->getErrorCode() === Cart::ERROR_CHECKOUT_VALIDATE) {
					$data['redirect'] = site_url('cart/cart');
				} else {
					$data['redirect'] = site_url('checkout/checkout');
				}
			} elseif (!$this->order->add()) {
				if ($this->order->hasError()) {
					$this->message->add('warning', $this->order->getError());
					$data['redirect'] = site_url('cart/cart');
				} else {
					$data['redirect'] = site_url('checkout/checkout');
				}
			} else {
				//If we are only reloading the totals section, do not include these other blocks
				if (empty($_GET['reload_totals'])) {
					$data['block_confirm_address'] = $this->block->render('checkout/confirm_address');

					$data['block_cart'] = $this->block->render('cart/cart', null, array('ajax_cart' => true));
				} else {
					$data['totals_only'] = true;
				}


				if ($this->config->get('coupon_status')) {
					$data['block_coupon'] = $this->block->render('cart/coupon', null, array('ajax' => true));
				}

				$data['block_totals'] = $this->block->render('cart/total');

				$data['reload_totals'] = site_url('block/checkout/confirm', 'reload_totals=1');

				$data['checkout_url'] = site_url('checkout/checkout');

				$data['payment'] = $this->cart->getPaymentMethod()->renderTemplate();
			}
		}

		$this->response->setOutput($this->render('block/checkout/confirm', $data));
	}

	public function check_order_status()
	{
		$json = array();

		if (isset($_GET['order_id'])) {
			$order = $this->order->get($_GET['order_id']);

			if ($order['confirmed']) {
				$json = array(
					'status'   => $this->order->getOrderStatus($order['order_status_id']),
					'redirect' => site_url('checkout/success'),
				);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
