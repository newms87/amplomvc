<?php
class Catalog_Controller_Block_Cart_Cart extends Controller
{
	public function index($settings = array())
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

		if ($this->config->get('config_customer_hide_price') && !$this->customer->isLogged()) {
			$this->data['no_price_display'] = _l("Please <a href=\"%s\">Login</a> or <a href=\"%s\">Register</a> to see Prices.", $this->url->link('account/login'), $this->url->link('account/register'));
		}

		if (!$this->cart->validate()) {
			$this->message->add('error', $this->cart->getError());
		}

		$show_return_policy = $this->config->get('config_cart_show_return_policy');

		//Get the cart Products
		if ($this->cart->hasProducts()) {
			$cart_products = $this->cart->getProducts();

			$image_width  = $this->config->get('config_image_cart_width');
			$image_height = $this->config->get('config_image_cart_height');

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

				$cart_product['href']   = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				$cart_product['remove'] = $this->url->link("cart/cart/remove", 'cart_key=' . $cart_product['key']);
			}
			unset($product);

			$this->data['cart_products'] = $cart_products;
		}

		// Gift Voucher
		if ($this->cart->hasVouchers()) {
			$vouchers = $this->cart->getVouchers();

			foreach ($vouchers as $voucher_id => &$voucher) {
				$voucher['amount_display'] = $this->currency->format($voucher['amount']);
			}
			unset($voucher);

			$this->data['cart_vouchers'] = $vouchers;
		}

		//Template Data
		$this->data['show_return_policy'] = $show_return_policy;

		//Url
		$this->data['url_cart'] = $this->url->link('cart/cart');
		$this->data['url_block_cart'] = $this->url->link("block/cart/cart");

		//Render Additional Carts
		$carts = $this->System_Extension_Cart->renderCarts();

		$this->data['cart_inline'] = $carts['inline'];
		$this->data['cart_extend'] = $carts['extend'];

		$this->data['cart_empty'] = $this->cart->isEmpty();

		//Ajax Messages
		if ($this->request->isAjax()) {
			$this->data['messages'] = $this->message->fetch();

			if ($this->data['cart_empty']) {
				$this->request->redirectBrowser($this->url->link('cart/cart'));
			}



			//TODO: how to handle ajax. Do we need $this->response->setOutput() for non-ajax calls?



		}

		//The Template
		$this->template->load('block/cart/cart');

		//Render
		$this->render();
	}
}
