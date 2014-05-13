<?php
class Catalog_Controller_Block_Cart_Cart extends Controller
{
	public function build($settings = array())
	{
		//Check if the shipping estimate was invalidated and that we are not in the checkout process
		// -> update the shipping estimate to the first Shipping option
		if (!$this->order->hasOrder() && !$this->cart->validateShippingMethod()) {
			$shipping_methods = $this->cart->getShippingMethods();

			if (!empty($shipping_methods)) {
				$this->cart->setShippingMethod(key($shipping_methods));
			}
		}

		if (!$this->cart->validate()) {
			if ($this->cart->getErrorCode() !== Cart::ERROR_CART_EMPTY) {
				$this->message->add('error', $this->cart->getError());
			}
		}

		$settings['show_price'] = !option('config_customer_hide_price') || $this->customer->isLogged();

		$show_return_policy = option('config_cart_show_return_policy');

		//Get the cart Products
		if ($this->cart->hasProducts()) {
			$cart_products = $this->cart->getProducts();

			foreach ($cart_products as &$cart_product) {
				$product = & $cart_product['product'];

				$cart_product['price'] = $this->tax->calculate($cart_product['price'], $product['tax_class_id']);
				$cart_product['total'] = $this->tax->calculate($cart_product['total'], $product['tax_class_id']);

				if ($show_return_policy) {
					$policy = $this->cart->getReturnPolicy($product['return_policy_id']);

					if ($policy['days'] > 0) {
						$product['return_policy'] = _l("%s Days", $policy['days']);
					} elseif ((int)$policy['days'] === 0) {
						$product['return_policy'] = _l("You may return at anytime");
					} else {
						$product['return_policy'] = $this->builder->finalSale();
					}
				}
			}
			unset($product);

			$settings['cart_products'] = $cart_products;
		}

		// Gift Voucher
		$settings['cart_vouchers'] = $this->cart->getVouchers();

		//Template Data
		$settings['show_return_policy'] = $show_return_policy;

		//Render Additional Carts
		$carts = $this->System_Extension_Cart->renderCarts();

		$settings['cart_inline'] = $carts['inline'];
		$settings['cart_extend'] = $carts['extend'];

		$settings['is_empty'] = $this->cart->isEmpty();
		$settings['is_ajax'] = $this->request->isAjax();

		//Render
		$this->render('block/cart/cart', $settings);
	}
}
