<?php
class Catalog_Controller_Account_Return extends Controller
{
	public function index()
	{
		$this->template->load('account/return_list');

		if (!$this->customer->isLogged()) {
			$this->session->set('redirect', $this->url->link('account/return'));

			$this->url->redirect('account/login');
		}

		$this->document->setTitle(_l("Product Returns"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Product Returns"), $this->url->link('account/return'));

		$sort_filter = $this->sort->getQueryDefaults('date_added', 'ASC');

		$return_total = $this->Model_Account_Return->getTotalReturns($sort_filter);
		$returns      = $this->Model_Account_Return->getReturns($sort_filter);

		foreach ($returns as &$return) {
			$return['name']       = $return['firstname'] . ' ' . $return['lastname'];
			$return['date_added'] = $this->date->format($return['date_added'], 'short');
			$return['href']       = $this->url->link('account/return/info', 'return_id=' . $return['return_id']);
		}

		$this->data['returns'] = $returns;

		//Template Data
		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $return_total;

		$this->data['pagination'] = $this->pagination->render();

		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	public function info()
	{
		$return_id = isset($_GET['return_id']) ? $_GET['return_id'] : 0;

		if (!$this->customer->isLogged()) {
			$query = array(
				'redirect' => $this->url->link('account/return/info', 'return_id=' . $return_id)
			);

			$this->url->redirect('account/login', $query);
		}

		//Page Title
		$this->document->setTitle(_l("Return Information"));

		$url_query = $this->url->getQuery('page');

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Product Returns"), $this->url->link('account/return', $url_query));
		$this->breadcrumb->add(_l("Return Information"), $this->url->link('account/return/info', 'return_id=' . $return_id . '&' . $url_query));

		$return_info = $this->Model_Account_Return->getReturn($return_id);

		if ($return_info) {
			$this->template->load('account/return_info');

			$return_info['comment']       = nl2br($return_info['comment']);
			$return_info['opened']        = $return_info['opened'] ? _l("Yes") : _l("No");
			$return_info['return_status'] = $this->order->getReturnStatus($return_info['return_status_id']);

			$this->data = $return_info;

			$this->data['date_ordered'] = $this->date->format($return_info['date_ordered'], 'date_format_short');
			$this->data['date_added']   = $this->date->format($return_info['date_added'], 'date_format_short');

			$histories = $this->Model_Account_Return->getReturnHistories($return_id);

			foreach ($histories as &$history) {
				$history['return_status'] = $this->order->getReturnStatus($history['return_status_id']);
				$history['date_added']    = $this->date->format($history['date_added'], 'date_format_short');
				$history['comment']       = nl2br($history['comment']);
			}
			unset($history);

			$this->data['histories'] = $histories;

			$this->data['continue'] = $this->url->link('account/return', $url_query);

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->setOutput($this->render());
		} else {
			$this->template->load('error/not_found');

			$this->data['page_title'] = _l("Return Information");

			$this->data['continue'] = $this->url->link('account/return');

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->setOutput($this->render());
		}
	}

	public function insert()
	{
		//Order ID
		$order_id   = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

		$order_lookup = isset($_GET['order_lookup']) ? $_GET['order_lookup'] : 0;

		if ($this->request->isPost() && $this->validate()) {
			$return_data = $_POST;

			foreach ($return_data['return_products'] as &$product) {
				$product['rma']      = $this->Model_Account_Return->generateRma($return_data + $product);
				$product['quantity'] = $product['return_quantity'];

				$product['return_id'] = $this->Model_Account_Return->addReturn($return_data + $product);
			}
			unset($product);

			$this->mail->sendTemplate('return', $return_data);

			$url_query = array(
				'return_ids' => array_column_recursive($_POST['return_products'], 'return_id'),
			);

			$this->url->redirect('account/return/success', $url_query);
		}

		//Page Head
		$this->document->setTitle(_l("Product Returns"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Returns"), $this->url->link('account/return'));
		$this->breadcrumb->add(_l("Product Returns"), $this->url->link('account/return/insert'));

		//The Data
		if ($this->request->isPost()) {
			$order_info = $_POST;
		} else {
			$order_info = array();
		}

		$customer_orders = $this->customer->getOrders();

		if ($customer_orders) {
			if ($order_lookup) {
				//If order does not belong to this customer, lookup the order info
				if (!empty($customer_orders) && !in_array($order_id, array_column_recursive($customer_orders, 'order_id'))) {
					$order_info = $this->order->get($order_id, false);

					//If the lookup email does not match the order email, customer may not view this order
					if (empty($_GET['email']) || $_GET['email'] !== $order_info['email']) {
						$this->message->add('warning', _l("This order ID %s is associated with another account! Please login to that account to request a return.", $order_id));
						$this->url->redirect('account/return/insert');
					}
				} //This order belongs to this customer, so they may request an exchange
				else {
					$order_lookup = false;
				}
			}

			if ($order_id) {
				foreach ($customer_orders as $order) {
					if ((int)$order['order_id'] === (int)$order_id) {
						$order_info += $order;
						break;
					}
				}
			} else {
				$order_info = reset($customer_orders);
			}
		}

		if ($order_info) {
			$order_info['date_ordered'] = $this->date->format($order_info['date_added']);

			$order_products = $this->System_Model_Order->getOrderProducts($order_info['order_id']);

			foreach ($order_products as $key => &$product) {
				$product_info = $this->Model_Catalog_Product->getProductInfo($product['product_id']);

				if ($product_info) {
					$product['name']  = $product_info['name'];
					$product['model'] = $product_info['model'];
					$product['price'] = $this->currency->format($product['price']);

					$return_policy = $this->cart->getReturnPolicy($product_info['return_policy_id']);

					if ($return_policy['days'] < 0) {
						$product['no_return'] = _l("Final Sale");
					} else {
						$return_date = $this->date->add($order_info['date_added'], $return_policy['days'] . ' days');

						if ($this->date->isInPast($return_date)) {
							$product['no_return'] = _l("Past Policy Return Date (%s)", $this->date->format($return_date, 'short'));
						}
					}

					$product['return_policy'] = $return_policy;

					$product_defaults = array(
						'return_quantity'  => 0,
						'return_reason_id' => '',
						'comment'          => '',
						'opened'           => 0,
					);

					foreach ($product_defaults as $key => $default) {
						if (isset($_POST['return_products'][$product['product_id']][$key])) {
							$product[$key] = $_POST['return_products'][$product['product_id']][$key];
						} else {
							$product[$key] = $default;
						}
					}

				} else {
					unset($order_products[$key]);
				}
			}
			unset($product);

			$order_info['return_products'] = $order_products;
		}

		$defaults = array(
			'order_id'     => $order_id,
			'date_ordered' => '',
			'firstname'    => $this->customer->info('firstname'),
			'lastname'     => $this->customer->info('lastname'),
			'email'        => $this->customer->info('email'),
			'telephone'    => $this->customer->info('telephone'),
			'captcha'      => '',
		);

		$this->data += $order_info + $defaults;

		if (!empty($customer_orders)) {
			foreach ($customer_orders as &$order) {
				$product_count = $this->System_Model_Order->getTotalOrderProducts($order['order_id']);

				$order['display'] = _l("%s - (%s products)", $order['order_id'], $product_count);
			}
			unset($order);
		}

		$this->data['customer_orders'] = $customer_orders;

		$this->data['date_ordered_display'] = $this->date->format($this->data['date_ordered'], 'short');
		$this->data['data_return_reasons']  = $this->order->getReturnReasons();

		$this->data['back']               = $this->url->link('account/account');
		$this->data['return_product_url'] = $this->url->link('account/return/insert');

		$this->data['order_lookup']        = $order_lookup;
		$this->data['order_lookup_action'] = $this->url->link('account/return/find');

		if (!$this->customer->isLogged()) {
			$this->message->add('warning', _l("You must be logged in to request a return. Your orders will automatically be associated to your account via your email address"));
		}

		$this->data['data_yes_no'] = array(
			0 => _l("No"),
		   1 => _l("Yes"),
		);

		//Action Buttons
		$this->data['action'] = $this->url->link('account/return/insert');
		$this->data['url_captcha_image'] = $this->url->link('account/return/captcha');

		//The Template
		$this->template->load('account/return_form');

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

	public function find()
	{
		$url_query = '';

		if ($this->request->isPost() && !empty($_POST['ol_order_id']) && !empty($_POST['ol_email'])) {
			$order = $this->order->get($_POST['ol_order_id']);

			if (!empty($order)) {
				if ($order['email'] === $_POST['ol_email']) {
					$query = array(
						'order_id'     => $order['order_id'],
						'email'        => $order['email'],
						'order_lookup' => 1,
					);

					$url_query = http_build_query($query);


					$this->message->add('notify', _l("The order was found! To request a return, you must first login or register a new account with the email %s.", $order['email']));
				} else {
					$this->message->add('warning', _l("That order is associated with another email account!"));
				}
			} else {
				$this->message->add("warning", _l("We were unable to find the order requested!"));
			}
		} else {
			$this->message->add("warning", _l("We were unable to find the order requested!"));
		}

		$this->url->redirect('account/return/insert', $url_query);
	}

	public function success()
	{
		$this->template->load('account/return_success');
		$this->document->setTitle(_l("Return Success"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Returns"), $this->url->link('account/return'));
		$this->breadcrumb->add(_l("Product Returns"), $this->url->link('account/return/insert'));
		$this->breadcrumb->add(_l("Return Success"), $this->url->link('account/return/success'));

		$returns = array();

		if (!empty($_GET['return_ids'])) {
			foreach ($_GET['return_ids'] as $return_id) {
				$returns[] = $this->Model_Account_Return->getReturn($return_id);
			}
		}

		$this->data['returns'] = $returns;

		$this->data['continue'] = $this->url->link('common/home');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (empty($_POST['order_id'])) {
			$this->error['order_id'] = _l("Order ID required!");
		}

		if (!$this->validation->text($_POST['firstname'], 1, 64)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 64 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 64)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 64 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		$has_product = false;

		if (!empty($_POST['return_products'])) {
			foreach ($_POST['return_products'] as $key => $product) {
				if (!empty($product['return_quantity'])) {
					$has_product = true;

					if (!isset($product['return_reason_id']) || (!$product['return_reason_id'] && $product['return_reason_id'] !== '0')) {
						$this->error["return_products[$product[product_id]][return_reason_id"] = _l("You must select a Reason For Return!");
					}
				}
			}
		}

		if (!$has_product) {
			$this->error['return_products'] = _l("You must select at least 1 product to return!");
		}

		if (!$this->captcha->validate($_POST['captcha'])) {
			$this->error['captcha'] = _l("Verification code does not match the image!");
		}

		return $this->error ? false : true;
	}

	public function captcha()
	{
		$this->captcha->generate();
	}
}
