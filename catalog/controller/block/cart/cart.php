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

		if (option('config_customer_hide_price') && !$this->customer->isLogged()) {
			$settings['no_price_display'] = _l("Please <a href=\"%s\">Login</a> or <a href=\"%s\">Register</a> to see Prices.", site_url('customer/login'), site_url('customer/registration'));
		}

		if (!$this->cart->validate()) {
			if ($this->cart->getErrorCode() !== Cart::ERROR_CART_EMPTY) {
				$this->message->add('error', $this->cart->getError());
			}
		}

		$show_return_policy = option('config_cart_show_return_policy');

		//Get the cart Products
		if ($this->cart->hasProducts()) {
			$cart_products = $this->cart->getProducts();

			$image_width  = option('config_image_cart_width');
			$image_height = option('config_image_cart_height');

			foreach ($cart_products as &$cart_product) {
				$product = & $cart_product['product'];

				if ($product['image']) {
					$cart_product['thumb'] = $this->image->resize($product['image'], $image_width, $image_height);
				}

				$cart_product['price_display'] = $this->currency->format($this->tax->calculate($cart_product['price'], $product['tax_class_id']));
				$cart_product['total_display'] = $this->currency->format($this->tax->calculate($cart_product['total'], $product['tax_class_id']));

				if ($product['reward']) {
					$product['reward'] = _l("Total Points: %s", $product['reward']);
				}

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

				$cart_product['href']   = site_url('product/product', 'product_id=' . $product['product_id']);
				$cart_product['remove'] = site_url("cart/cart/remove", 'cart_key=' . $cart_product['key']);
			}
			unset($product);

			$settings['cart_products'] = $cart_products;
		}

		// Gift Voucher
		if ($this->cart->hasVouchers()) {
			$vouchers = $this->cart->getVouchers();

			foreach ($vouchers as $voucher_id => &$voucher) {
				$voucher['amount_display'] = $this->currency->format($voucher['amount']);
			}
			unset($voucher);

			$settings['cart_vouchers'] = $vouchers;
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
