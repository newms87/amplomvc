<?php
class Catalog_Controller_Account_Wishlist extends Controller
{
	public function index()
	{
		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/wishlist'));

			$this->url->redirect('customer/login');
		}

		if (!$this->session->has('wishlist')) {
			$this->session->set('wishlist', array());
		}

		if (isset($_GET['remove'])) {
			$key = array_search($_GET['remove'], $this->session->get('wishlist'));

			if ($key !== false) {
				unset($this->session->get('wishlist')[$key]);
			}

			$this->message->add('success', _l("Success: You have modified your wishlist!"));

			$this->url->redirect('account/wishlist');
		}

		$this->document->setTitle(_l("My Wish List"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("My Wish List"), $this->url->link('account/wishlist'));

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->delete('success');
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();

		foreach ($this->session->get('wishlist') as $key => $product_id) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
				} else {
					$image = false;
				}

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = _l("In Stock");
				}

				if (($this->config->get('config_customer_hide_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_hide_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id']));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id']));
				} else {
					$special = false;
				}

				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'      => $image,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'])
				);
			} else {
				unset($this->session->get('wishlist')[$key]);
			}
		}

		$data['continue'] = $this->url->link('account/account');

		$this->response->setOutput($this->render('account/wishlist', $data));
	}

	public function add()
	{
		$json = array();

		if (!$this->session->has('wishlist')) {
			$this->session->set('wishlist', array());
		}

		if (isset($_POST['product_id'])) {
			$product_id = $_POST['product_id'];
		} else {
			$product_id = 0;
		}

		$product_info = $this->Model_Catalog_Product->getProduct($product_id);

		if ($product_info) {
			if (!in_array($_POST['product_id'], $this->session->get('wishlist'))) {
				$this->session->get('wishlist')[] = $_POST['product_id'];
			}

			if ($this->customer->isLogged()) {
				$json['success'] = sprintf(_l("Success: You have added <a href=\"%s\">%s</a> to your <a href=\"%s\">wish list</a>!"), $this->url->link('product/product', 'product_id=' . $_POST['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
			} else {
				$json['success'] = sprintf(_l("You must <a href=\"%s\">login</a> or <a href=\"%s\">create an account</a> to save <a href=\"%s\">%s</a> to your <a href=\"%s\">wish list</a>!"), $this->url->link('customer/login'), $this->url->link('customer/registration'), $this->url->link('product/product', 'product_id=' . $_POST['product_id']), $product_info['name'], $this->url->link('account/wishlist'));
			}

			$json['total'] = sprintf(_l("Wish List (%s)"), ($this->session->has('wishlist') ? count($this->session->get('wishlist')) : 0));
		}

		$this->response->setOutput(json_encode($json));
	}
}
