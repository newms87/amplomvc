<?php

class Catalog_Controller_Cart_Cart extends Controller
{
	public function index($data = array())
	{
		//Page Head
		$this->document->setTitle(_l("Shopping Cart"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), site_url('cart/cart'));

		//We remove any active orders to allow shipping estimates to be updated
		if ($this->order->hasOrder()) {
			$this->order->clear();
		}

		$data += array(
			'show_total'      => option('config_show_cart_total', true),
			'show_weight'     => option('config_show_cart_weight'),
			'show_coupons'    => option('coupon_status'),
			'show_vouchers'   => option('voucher_status'),
			'show_rewards'    => option('reward_status'),
			'has_shipping'    => option('shipping_status') && $this->cart->hasShipping(),
			'weight'          => $this->cart->getWeight(),
			'customer_points' => $this->customer->getRewardPoints(),
			'cart_points'     => $this->cart->getTotalPoints(),
			'is_empty'        => $this->cart->isEmpty(),
			'can_checkout'    => $this->cart->canCheckout(),
		);

		//Set Continue to the redirect unless we are redirecting to the cart page
		if (isset($_GET['redirect']) && !preg_match("/cart\\/cart/", $_GET['redirect'])) {
			$continue = urldecode($_GET['redirect']);
		} else {
			$continue = site_url('product/category');
		}

		//Action
		$data += array(
			'continue' => $continue,
		   'checkout' => site_url('checkout/checkout'),
		);

		//Render
		$this->response->setOutput($this->render('cart/cart', $data));
	}

	public function buy_now()
	{
		$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
		$type       = !empty($_POST['type']) ? $_POST['type'] : Cart::PRODUCTS;
		$quantity   = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
		$options    = !empty($_POST['options']) ? $_POST['options'] : array();

		$key = $this->addToCart($type, $product_id, $quantity, $options);

		if ($key) {
			if ($type === Cart::PRODUCTS) {
				$name = $this->Model_Catalog_Product->getProductName($product_id);
			}

			$this->request->setRedirect(site_url('product/product', 'product_id=' . $product_id));

			$url_product = site_url('product/product', 'product_id=' . $product_id);
			$url_cart    = site_url('cart/cart');
			$this->message->add('success', _l('<a href="%s">%s</a> has been added to <a href="%s">the cart</a>', $url_product, $name, $url_cart));
		} else {
			$this->message->add('error', $this->cart->getError('add'));

			redirect('product/product', 'product_id=' . $product_id);
		}

		redirect('checkout/checkout');
	}

	public function add()
	{
		$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
		$type       = !empty($_POST['type']) ? $_POST['type'] : Cart::PRODUCTS;
		$quantity   = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
		$options    = !empty($_POST['options']) ? $_POST['options'] : array();

		$key = $this->addToCart($type, $product_id, $quantity, $options);

		if ($key) {
			if ($type === Cart::PRODUCTS) {
				$name = $this->Model_Catalog_Product->getProductName($product_id);
			}

			$this->request->setRedirect(site_url('product/product', 'product_id=' . $product_id));

			$url_product = site_url('product/product', 'product_id=' . $product_id);
			$url_cart    = site_url('cart/cart');
			$this->message->add('success', _l('<a href="%s">%s</a> has been added to <a href="%s">the cart</a>', $url_product, $name, $url_cart));

			$item_count = $this->cart->countItems();
			$total      = $this->currency->format($this->cart->getTotal());
			$this->message->add('total', _l('%s item(s) - %s', $item_count, $total));

			$this->message->add('key', $key);
		} else {
			$this->message->add('error', $this->cart->getError('add'));
		}

		$this->response->setOutput($this->message->toJSON());
	}

	private function addToCart()
	{
		$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
		$type       = !empty($_POST['type']) ? $_POST['type'] : Cart::PRODUCTS;
		$quantity   = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
		$options    = !empty($_POST['options']) ? $_POST['options'] : array();

		if ($type === Cart::PRODUCTS) {
			$key = $this->cart->addProduct($product_id, $quantity, $options);
		} elseif ($type === Cart::VOUCHERS) {
			$key = $this->cart->addVoucher($product_id, $quantity, $options);
		} else {
			$key = $this->cart->addItem($type, $product_id, $quantity, $options);
		}

		return $this->cart->hasError('add') ? false : $key;
	}

	public function remove()
	{
		$cart_product = $this->cart->getProduct($_GET['cart_key'], true);

		$this->message->add('success', _l('<a href="%s">%s</a> has been removed from your cart.', site_url('product/product', 'product_id=' . $cart_product['id']), $cart_product['product']['name']));

		$this->cart->removeProduct($_GET['cart_key']);

		if (!$this->request->isAjax()) {
			redirect('cart/cart');
		}
	}
}
