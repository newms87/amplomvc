<?php
class Catalog_Controller_Cart_Cart extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Shopping Cart"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shopping Cart"), $this->url->link('cart/cart'));

		$this->data['block_cart'] = $this->getBlock('cart/cart');

		//We remove any active orders to allow shipping estimates to be updated
		if ($this->order->hasOrder()) {
			$this->order->clear();
		}

		if ($this->config->get('config_show_cart_weight')) {
			$this->data['weight'] = $this->weight->format($this->cart->getWeight());
		}

		if ($this->config->get('coupon_status')) {
			$this->data['block_coupon'] = $this->getBlock('cart/coupon');
		}

		if ($this->config->get('voucher_status')) {
			$this->data['block_voucher'] = $this->getBlock('cart/voucher');
		}

		if ($this->config->get('reward_status') && $this->customer->getRewardPoints() && $this->cart->getTotalPoints() > 0) {
			$this->data['block_reward'] = $this->getBlock('cart/reward');
		}

		if ($this->config->get('shipping_status') && $this->cart->hasShipping()) {
			$this->data['block_shipping'] = $this->getBlock('cart/shipping');
		}

		$this->data['block_total'] = $this->getBlock('cart/total');

		$this->data['cart_empty']   = $this->cart->isEmpty();
		$this->data['can_checkout'] = $this->cart->canCheckout();

		//Set Continue to the redirect unless we are redirecting to the cart page
		if (isset($_GET['redirect']) && preg_match("/cart\\/cart/", $_GET['redirect']) == 0) {
			$this->data['continue'] = urldecode($_GET['redirect']);
		} else {
			$this->data['continue'] = $this->url->link('product/category');
		}

		$this->data['checkout'] = $this->url->link('checkout/checkout');

		//The Template
		$this->view->load('cart/cart');

		//Dependencies
		$this->children = array(
			'area/left',
			'area/right',
			'area/top',
			'area/bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
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

			$this->request->setRedirect($this->url->link('product/product', 'product_id=' . $product_id));

			$url_product = $this->url->link('product/product', 'product_id=' . $product_id);
			$url_cart    = $this->url->link('cart/cart');
			$this->message->add('success', _l('<a href="%s">%s</a> has been added to <a href="%s">the cart</a>', $url_product, $name, $url_cart));
		} else {
			$this->message->add('error', $this->cart->getError('add'));

			$this->url->redirect('product/product', 'product_id=' . $product_id);
		}

		$this->url->redirect('checkout/checkout');
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

			$this->request->setRedirect($this->url->link('product/product', 'product_id=' . $product_id));

			$url_product = $this->url->link('product/product', 'product_id=' . $product_id);
			$url_cart    = $this->url->link('cart/cart');
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
		}
		elseif ($type === Cart::VOUCHERS) {
			$key = $this->cart->addVoucher($product_id, $quantity, $options);
		}
		else {
			$key = $this->cart->addItem($type, $product_id, $quantity, $options);
		}

		return $this->cart->hasError('add') ? false : $key;
	}

	public function remove()
	{
		$cart_product = $this->cart->getProduct($_GET['cart_key'], true);

		$this->message->add('success', _l('<a href="%s">%s</a> has been removed from your cart.', $this->url->link('product/product', 'product_id=' . $cart_product['id']), $cart_product['product']['name']));

		$this->cart->removeProduct($_GET['cart_key']);

		if (!$this->request->isAjax()) {
			$this->url->redirect('cart/cart');
		}
	}
}
