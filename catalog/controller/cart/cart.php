<?php
class Catalog_Controller_Cart_Cart extends Controller
{
	public function index()
	{
		$this->language->load('cart/cart');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('cart/cart'));

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

		$this->data['cart_empty'] = $this->cart->isEmpty();
		$this->data['can_checkout'] = $this->cart->canCheckout();

		//Set Continue to the redirect unless we are redirecting to the cart page
		if (isset($_GET['redirect']) && preg_match("/cart\\/cart/", $_GET['redirect']) == 0) {
			$this->data['continue'] = urldecode($_GET['redirect']);
		} else {
			$this->data['continue'] = $this->url->link('product/category');
		}

		$this->data['checkout'] = $this->url->link('checkout/checkout');

		//The Template
		$this->template->load('cart/cart');

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
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

		$this->cart->add($type, $product_id, $quantity, $options);

		if (!$this->cart->hasError('add')) {
			if ($type === Cart::PRODUCTS) {
				$name = $this->Model_Catalog_Product->getProductName($product_id);
			}

			$this->url->setRedirect('product/product', 'product_id=' . $product_id);

			$url_product = $this->url->link('product/product', 'product_id=' . $product_id);
			$url_cart    = $this->url->link('cart/cart');
			$this->message->add('success', _l('<a href="%s">%s</a> has been added to <a href="%s">the cart</a>', $url_product, $name, $url_cart));
		} else {
			$this->message->add('error', $this->cart->get_errors('add'));
		}

		$this->url->redirect('checkout/checkout');
	}

	public function add()
	{
		$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
		$type       = !empty($_POST['type']) ? $_POST['type'] : Cart::PRODUCTS;
		$quantity   = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
		$options    = !empty($_POST['options']) ? $_POST['options'] : array();

		$key = $this->cart->add($type, $product_id, $quantity, $options);

		if (!$this->cart->hasError('add')) {
			if ($type === Cart::PRODUCTS) {
				$name = $this->Model_Catalog_Product->getProductName($product_id);
			}

			$this->url->setRedirect('product/product', 'product_id=' . $product_id);

			$url_product = $this->url->link('product/product', 'product_id=' . $product_id);
			$url_cart    = $this->url->link('cart/cart');
			$this->message->add('success', _l('<a href="%s">%s</a> has been added to <a href="%s">the cart</a>', $url_product, $name, $url_cart));

			$item_count = $this->cart->countItems();
			$total      = $this->currency->format($this->cart->getTotal());
			$this->message->add('total', _l('%s item(s) - %s', $item_count, $total));

			$this->message->add('key', $key);
		} else {
			$this->message->add('error', $this->cart->get_errors('add'));
		}

		$this->response->setOutput($this->message->toJSON());
	}
}
