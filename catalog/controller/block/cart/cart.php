<?php
class Catalog_Controller_Block_Cart_Cart extends Controller
{
	public function build($settings = array())
	{
		if ($this->request->isPost()) {
			//Update Product
			if (isset($_POST['cart_update'])) {
				if (!empty($_POST['quantity'])) {
					foreach ($_POST['quantity'] as $key => $quantity) {
						$this->cart->updateProduct($key, $quantity);
					}

					$this->message->add('success', _l("Your cart has been updated!"));
				}
			} elseif (isset($_POST['cart_remove_voucher'])) {
				$this->cart->removeVoucher($_POST['remove_voucher']);
				$this->message->add('success', _l('The voucher was removed from your cart.'));
			}
		}

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
		if ($this->cart->hasVouchers()) {
			$settings['cart_vouchers'] = $this->cart->getVouchers();
		}

		//Template Data
		$settings['show_return_policy'] = $show_return_policy;

		//Url
		$settings['url_cart'] = site_url('cart/cart');
		$settings['url_block_cart'] = site_url("block/cart/cart");

		//Render Additional Carts
		$carts = $this->System_Extension_Cart->renderCarts();

		$settings['cart_inline'] = $carts['inline'];
		$settings['cart_extend'] = $carts['extend'];

		$settings['cart_empty'] = $this->cart->isEmpty();

		$settings['is_ajax'] = $this->request->isAjax();

		//Ajax Messages
		if ($this->request->isAjax()) {
			$settings['messages'] = $this->message->fetch();

			if ($settings['cart_empty']) {
				$this->request->redirectBrowser(site_url('cart/cart'));
			}
		}

		//Render
		$this->render('block/cart/cart', $settings);
	}
}
