<?php
class Admin_Controller_Sale_Order extends Controller
{
	public function index()
	{
		//Page Head
		$this->document->setTitle(_l("Orders"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Orders"), site_url('sale/order'));

		//The Table Columns
		$columns = array();

		$columns['customer'] = array(
			'type'         => 'text',
			'display_name' => _l("Customer"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['total'] = array(
			'type'         => 'int',
			'display_name' => _l("Total"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['store_id'] = array(
			'type'         => 'select',
			'display_name' => _l("Store"),
			'filter'       => true,
			'build_config' => array(
				'store_id',
				'name'
			),
			'build_data'   => $this->Model_Setting_Store->getStores(),
			'sortable'     => false,
		);

		$columns['order_status_id'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'filter'       => true,
			'build_config' => array(
				false,
				'title'
			),
			'build_data'   => $this->order->getOrderStatuses(),
			'sortable'     => true,
		);

		$columns['date_added'] = array(
			'type'         => 'date',
			'display_name' => _l("Date Added"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['date_modified'] = array(
			'type'         => 'date',
			'display_name' => _l("Date Modified"),
			'filter'       => true,
			'sortable'     => true,
		);

		//Get Sorted / Filtered Data
		$sort   = $this->sort->getQueryDefaults('order_id', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		$order_total = $this->System_Model_Order->getTotalOrders($filter);
		$orders      = $this->System_Model_Order->getOrders($sort + $filter);

		$url_query = $this->url->getQueryExclude('order_id');

		foreach ($orders as &$order) {
			$order['actions'] = array(
				'view' => array(
					'text' => _l("View"),
					'href' => site_url('sale/order/info', 'order_id=' . $order['order_id'] . '&' . $url_query)
				),
			);

			if ($this->order->isEditable($order)) {
				$action['edit'] = array(
					'text' => _l("Edit"),
					'href' => site_url('sale/order/update', 'order_id=' . $order['order_id'] . '&' . $url_query)
				);
			}

			$customer = $this->customer->getCustomer($order['customer_id']);

			if ($customer) {
				$order['customer'] = $customer['firstname'] . ' ' . $customer['lastname'];
			} elseif ($order['customer_id']) {
				$order['customer'] = "Unknown Customer ID ($order[customer_id])";
			} else {
				$order['customer'] = 'Guest';
			}

			$order['total']         = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
			$order['date_added']    = $this->date->format($order['date_added'], 'short');
			$order['date_modified'] = $this->date->format($order['date_modified'], 'short');
		}

		//Build The Table
		$tt_data = array(
			'row_id' => 'order_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($orders);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$data['list_view'] = $this->table->render();

		//Render Limit Menu
		$data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $order_total;

		$data['pagination'] = $this->pagination->render();

		//Action Buttons
		$data['invoice'] = site_url('sale/order/invoice');
		$data['insert']  = site_url('sale/order/insert');
		$data['delete']  = site_url('sale/order/delete');

		//Render
		$this->response->setOutput($this->render('sale/order_list', $data));
	}

	public function update()
	{
		if ($this->request->isPost() && $this->validateForm()) {
			//Insert
			if (!isset($_GET['order_id'])) {
				$this->System_Model_Order->addOrder($_POST);
			} //Update
			else {
				$this->System_Model_Order->editOrder($_GET['order_id'], $_POST);
			}

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified orders!"));

				redirect('sale/order');
			}
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Orders"));

		if (!empty($_GTE['order_id']) && $this->validateDelete()) {
			$this->System_Model_Order->deleteOrder($_GET['order_id']);

			if (!$this->message->has('error', 'warning')) {
				$this->message->add('success', _l("Success: You have modified orders!"));

				redirect('sale/order');
			}
		}

		$this->index();
	}

	public function getForm()
	{
		//Page Head
		$this->document->setTitle(_l("Orders"));

		//Insert or Update
		$order_id = !empty($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Orders"), site_url('sale/order'));

		if ($order_id) {
			$this->breadcrumb->add(_l("Edit"), site_url('sale/order/update', 'order_id=' . $order_id));
		} else {
			$this->breadcrumb->add(_l("Add"), site_url('sale/order/update'));
		}

		//Load Information
		if ($order_id && !$this->request->isPost()) {
			$order_info = $this->order->get($order_id);

			if ($order_info) {
				$order_info['customer'] = $this->Model_Sale_Customer->getCustomer($order_info['customer_id']);
				//TODO: Keep this? Need further implementation...
				$order_info['customer_addresses'] = $this->Model_Sale_Customer->getAddresses($order_info['customer_id']);
				$order_info['order_products']     = $this->System_Model_Order->getOrderProducts($order_id);
				$order_info['order_vouchers']     = $this->System_Model_Order->getOrderVouchers($order_id);
				$order_info['order_totals']       = $this->System_Model_Order->getOrderTotals($order_id);
			}
		}

		//Add Info / defaults to Template
		$defaults = array(
			'store_id'           => '',
			'customer'           => array(),
			'customer_id'        => '',
			'customer_group_id'  => '',
			'firstname'          => '',
			'lastname'           => '',
			'email'              => '',
			'telephone'          => '',
			'fax'                => '',
			'order_status_id'    => '',
			'comment'            => '',
			'customer_addresses' => array(),

			//TODO: how best to handle this?
			'shipping_address'   => array(),
			'payment_address'    => array(),

			'shipping_code' => '',
			'payment_code'  => '',
			'order_products'     => array(),
			'order_vouchers'     => array(),
			'order_totals'       => array(),
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($order_info[$key])) {
				$data[$key] = $order_info[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Template Data
		foreach ($data['order_products'] as &$order_product) {
			$order_product['options']   = $this->System_Model_Order->getOrderOptions($order_id, $order_product['order_product_id']);
			$order_product['downloads'] = $this->System_Model_Order->getOrderDownloads($order_id, $order_product['order_product_id']);
		}
		unset($order_product);

		$data['data_order_statuses'] = $this->order->getOrderStatuses();
		$data['data_stores']         = $this->Model_Setting_Store->getStores();
		$data['data_countries']      = $this->Model_Localisation_Country->getCountries();
		$data['data_voucher_themes'] = $this->Model_Sale_VoucherTheme->getVoucherThemes();

		//Urls
		$data['store_url'] = URL_SITE;

		//Action Buttons
		$data['save']   = site_url('sale/order/update', 'order_id=' . $order_id);
		$data['cancel'] = site_url('sale/order');

		//Render
		$this->response->setOutput($this->render('sale/order_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/order')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify orders!");
		}

		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		if (!$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		//Validate Payment Information
		if (!$this->validation->text($_POST['payment_firstname'], 1, 32)) {
			$this->error['payment_firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['payment_lastname'], 1, 32)) {
			$this->error['payment_lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['payment_address_1'], 3, 128)) {
			$this->error['payment_address_1'] = _l("Address 1 must be between 3 and 128 characters!");
		}

		if (!$this->validation->text($_POST['payment_city'], 3, 128)) {
			$this->error['payment_city'] = _l("City must be between 3 and 128 characters!");
		}

		$country_info = $this->Model_Localisation_Country->getCountry($_POST['payment_country_id']);

		if (!$country_info) {
			$this->error['payment_country'] = _l("Please select a country!");
		} elseif ($country_info['postcode_required'] && (!$this->validation->text($_POST['payment_postcode'], 2, 10))) {
			$this->error['payment_postcode'] = _l("Postcode must be between 2 and 10 characters for this country!");
		}

		if (empty($_POST['payment_zone_id'])) {
			$this->error['payment_zone'] = _l("Please select a region / state!");
		}

		// Check if any products require shipping
		$shipping = false;

		if (isset($_POST['order_product'])) {
			foreach ($_POST['order_product'] as $order_product) {
				$product_info = $this->Model_Catalog_Product->getProduct($order_product['product_id']);

				if ($product_info && $product_info['shipping']) {
					$shipping = true;
				}
			}
		}

		if ($shipping) {
			if ((strlen($_POST['shipping_firstname']) < 1) || (strlen($_POST['shipping_firstname']) > 32)) {
				$this->error['shipping_firstname'] = _l("First Name must be between 1 and 32 characters!");
			}

			if ((strlen($_POST['shipping_lastname']) < 1) || (strlen($_POST['shipping_lastname']) > 32)) {
				$this->error['shipping_lastname'] = _l("Last Name must be between 1 and 32 characters!");
			}

			if ((strlen($_POST['shipping_address_1']) < 3) || (strlen($_POST['shipping_address_1']) > 128)) {
				$this->error['shipping_address_1'] = _l("Address 1 must be between 3 and 128 characters!");
			}

			if ((strlen($_POST['shipping_city']) < 3) || (strlen($_POST['shipping_city']) > 128)) {
				$this->error['shipping_city'] = _l("City must be between 3 and 128 characters!");
			}

			$country_info = $this->Model_Localisation_Country->getCountry($_POST['shipping_country_id']);

			if ($country_info && $country_info['postcode_required'] && (strlen($_POST['shipping_postcode']) < 2) || (strlen($_POST['shipping_postcode']) > 10)) {
				$this->error['shipping_postcode'] = _l("Postcode must be between 2 and 10 characters for this country!");
			}

			if ($_POST['shipping_country_id'] == '') {
				$this->error['shipping_country'] = _l("Please select a country!");
			}

			if ($_POST['shipping_zone_id'] == '') {
				$this->error['shipping_zone'] = _l("Please select a region / state!");
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = _l("Warning: Please check the form carefully for errors!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/order')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify orders!");
		}

		return empty($this->error);
	}

	public function info()
	{
		$order_id = !empty($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

		$order_info = $this->System_Model_Order->getOrder($order_id);

		//Order Not Found
		if (!$order_info) {
			$this->message->add("warning", _l("The Order was not found in the system"));
			redirect('sale/order');
		}

		//Page Head
		$this->document->setTitle(_l("Orders"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Orders"), site_url('sale/order'));
		$this->breadcrumb->add($order_info['invoice_id'], site_url('sale/order/info', 'order_id=' . $order_id));

		$data += $order_info;

		$transaction = $this->transaction->get($order_info['transaction_id']);
		$shipping = $this->shipping->get($order_info['shipping_id']);

		$data['payment_method']  = $this->System_Extension_Payment->get($transaction['payment_code'])->info();
		$data['payment_address'] = $this->address->format($this->order->getPaymentAddress($order_id));

		if ($shipping && $shipping['shipping_code']) {
			$data['shipping_method']  = $this->System_Extension_Shipping->get($shipping['shipping_code'])->info();
			$data['shipping_address'] = $this->address->format($this->order->getShippingAddress($order_id));
		}

		if ($order_info['customer_id']) {
			$data['url_customer'] = site_url('sale/customer/update', 'customer_id=' . $order_info['customer_id']);
		}

		$customer_group_info = $this->Model_Sale_CustomerGroup->getCustomerGroup($order_info['customer_group_id']);

		if ($customer_group_info) {
			$data['customer_group'] = $customer_group_info['name'];
		}

		$data['comment'] = nl2br($order_info['comment']);
		$data['total']   = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

		$data['credit']       = max(0, -1 * $order_info['total']);
		//$data['credit_total'] = $this->Model_Sale_Customer->getTotalTransactionsByOrderId($order_id);
		$data['reward_total'] = $this->Model_Sale_Customer->getTotalCustomerRewardsByOrderId($order_id);

		$data['order_status'] = $this->order->getOrderStatus($order_info['order_status_id']);

		$data['date_added']    = $this->date->format($order_info['date_added'], 'datetime_long');
		$data['date_modified'] = $this->date->format($order_info['date_modified'], 'datetime_long');

		//Order Products
		$products = $this->System_Model_Order->getOrderProducts($order_id);

		foreach ($products as &$product) {
			$product += $this->Model_Catalog_Product->getProduct($product['product_id']);

			$product['options'] = $this->System_Model_Order->getOrderProductOptions($order_id, $product['order_product_id']);

			$product['price_display'] = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
			$product['total_display'] = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
			$product['href']          = site_url('catalog/product/update', 'product_id=' . $product['product_id']);
		}
		unset($product);

		$data['products'] = $products;

		$vouchers = $this->System_Model_Order->getOrderVouchers($order_id);

		foreach ($vouchers as &$voucher) {
			$voucher['amount_display'] = $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
			$voucher['href']           = site_url('sale/voucher/update', 'voucher_id=' . $voucher['voucher_id']);
		}
		unset($voucher);

		$data['vouchers'] = $vouchers;

		$totals = $this->System_Model_Order->getOrderTotals($order_id);

		foreach ($totals as &$total) {
			$total['value_display'] = $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']);
		}
		unset($total);

		$data['totals'] = $totals;

		$data['downloads'] = $this->System_Model_Order->getOrderDownloads($order_id);

		$data['data_order_statuses'] = $this->order->getOrderStatuses();

		// Fraud
		$data['fraud'] = $this->Model_Sale_Fraud->getFraud($order_info['order_id']);

		//Store
		$data['store'] = $this->Model_Setting_Store->getStore($order_info['store_id']);

		//History
		$data['history'] = $this->history();

		//Action Buttons
		$data['invoice'] = site_url('sale/order/invoice', 'order_id=' . $order_id);
		$data['cancel']  = site_url('sale/order');

		//Render
		$this->response->setOutput($this->render('sale/order_info', $data));
	}

	public function createInvoiceNo()
	{
		$order_id = !empty($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

		if (!$order_id) {
			$json = array();

			if (!$this->user->can('modify', 'sale/order')) {
				$json['error'] = _l("Warning: You do not have permission to modify orders!");
			} else {
				$invoice_no = $this->System_Model_Order->generateInvoiceId($order_id);

				if ($invoice_no) {
					$json['invoice_no'] = $invoice_no;
				} else {
					$json['error'] = _l("Warning: Could not complete this action!");
				}
			}

			$this->response->setOutput(json_encode($json));
		}
	}

	public function addCredit()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/order')) {
			$json['error'] = _l("Warning: You do not have permission to modify orders!");
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);

			if ($order_info && $order_info['customer_id']) {
				$credit_total = $this->Model_Sale_Customer->getTotalTransactionsByOrderId($_GET['order_id']);

				if (!$credit_total) {
					$this->Model_Sale_Customer->addTransaction($order_info['customer_id'], _l("Order ID:") . ' #' . $_GET['order_id'], $order_info['total'], $_GET['order_id']);

					$json['success'] = _l("Success: Account credit added!");
				} else {
					$json['error'] = _l("Warning: Could not complete this action!");
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function removeCredit()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/order')) {
			$json['error'] = _l("Warning: You do not have permission to modify orders!");
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);

			if ($order_info && $order_info['customer_id']) {
				$this->Model_Sale_Customer->deleteTransaction($_GET['order_id']);

				$json['success'] = _l("Success: Account credit removed!");
			} else {
				$json['error'] = _l("Warning: Could not complete this action!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function addReward()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/order')) {
			$json['error'] = _l("Warning: You do not have permission to modify orders!");
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);

			if ($order_info && $order_info['customer_id']) {
				$reward_total = $this->Model_Sale_Customer->getTotalCustomerRewardsByOrderId($_GET['order_id']);

				if (!$reward_total) {
					$this->Model_Sale_Customer->addReward($order_info['customer_id'], _l("Order ID:") . ' #' . $_GET['order_id'], $order_info['reward'], $_GET['order_id']);

					$json['success'] = _l("Success: Reward points added!");
				} else {
					$json['error'] = _l("Warning: Could not complete this action!");
				}
			} else {
				$json['error'] = _l("Warning: Could not complete this action!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function removeReward()
	{
		$json = array();

		if (!$this->user->can('modify', 'sale/order')) {
			$json['error'] = _l("Warning: You do not have permission to modify orders!");
		} elseif (isset($_GET['order_id'])) {
			$order_info = $this->Model_Sale_Order->getOrder($_GET['order_id']);

			if ($order_info && $order_info['customer_id']) {
				$this->Model_Sale_Customer->deleteReward($_GET['order_id']);

				$json['success'] = _l("Success: Reward points removed!");
			} else {
				$json['error'] = _l("Warning: Could not complete this action!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function history()
	{
		if (empty($_GET['order_id'])) {
			return;
		}

		$order_id = (int)$_GET['order_id'];

		if ($this->request->isPost()) {
			if (!$this->user->can('modify', 'sale/order')) {
				$this->message->add('warning', _l("Warning: You do not have permission to modify orders!"));
			} else {
				$result = $this->order->updateOrder($order_id, $_POST['order_status_id'], $_POST['comment'], !empty($_POST['notify']) ? 1 : 0);

				if ($result) {
					$this->message->add('success', _l("Success: You have modified orders!"));
				} else {
					$this->message->add('error', $this->order->getError());
				}
			}
		}

		$filter = array(
			'order_ids' => array($order_id),
		);

		$histories = $this->System_Model_Order->getOrderHistories($filter);

		foreach ($histories as &$history) {
			$history['notify']     = $history['notify'] ? _l("Yes") : _l("No");
			$history['comment']    = nl2br($history['comment']);
			$history['date_added'] = $this->date->format($history['date_added'], 'datetime_long');
			$status                = $this->order->getOrderStatus($history['order_status_id']);
			$history['status']     = $status['title'];
		}
		unset($history);

		$data['histories'] = $histories;
	}

	public function download()
	{
		if (isset($_GET['order_option_id'])) {
			$order_option_id = $_GET['order_option_id'];
		} else {
			$order_option_id = 0;
		}

		$option_info = $this->Model_Sale_Order->getOrderOption($_GET['order_id'], $order_option_id);

		if ($option_info && $option_info['type'] == 'file') {
			$file = DIR_DOWNLOAD . $option_info['value'];
			$mask = basename(substr($option_info['value'], 0, strrpos($option_info['value'], '.')));

			if (!headers_sent()) {
				if (file_exists($file)) {
					$this->export->downloadFile($file, $mask);
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->document->setTitle(_l("Orders"));

			$this->breadcrumb->add(_l("Home"), site_url('common/home'));
			$this->breadcrumb->add(_l("Orders"), site_url('error/not_found'));

			$this->response->setOutput($this->render('error/not_found', $data));
		}
	}

	public function upload()
	{
		$json = array();

		if ($this->request->isPost()) {
			if (!empty($_FILES['file']['name'])) {
				$filename = html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8');

				if ((strlen($filename) < 3) || (strlen($filename) > 128)) {
					$json['error'] = _l("Filename must be between 3 and 128 characters!");
				}

				$allowed = array();

				$filetypes = explode(',', $this->config->get('config_upload_allowed'));

				foreach ($filetypes as $filetype) {
					$allowed[] = trim($filetype);
				}

				if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
					$json['error'] = _l("Invalid file type!");
				}

				if (!$this->validation->fileUpload($_FILES['file'])) {
					$json['error'] = $this->validation->getError();
				}

			} else {
				$json['error'] = _l("Error uploading file to server!");
			}

			if (!isset($json['error'])) {
				if (is_uploaded_file($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
					$file = basename($filename) . '.' . md5(rand());

					$json['file'] = $file;

					move_uploaded_file($_FILES['file']['tmp_name'], DIR_DOWNLOAD . $file);
				}

				$json['success'] = _l("Your file was successfully uploaded!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function invoice()
	{
		$data['title'] = _l("Orders");

		$data['base'] = URL_SITE;

		$data['language'] = $this->language->info('code');

		$data['orders'] = array();

		$orders = array();

		if (isset($_GET['selected'])) {
			$orders = $_GET['selected'];
		} elseif (isset($_GET['order_id'])) {
			$orders[] = $_GET['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->System_Model_Order->getOrder($order_id);

			if ($order_info) {
				$store_info = $this->config->loadGroup('config', $order_info['store_id']);

				if ($store_info) {
					$store_address   = $store_info['config_address'];
					$store_email     = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax       = $store_info['config_fax'];
				} else {
					$store_address   = $this->config->get('config_address');
					$store_email     = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax       = $this->config->get('config_fax');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$shipping_address = str_replace(array(
					"\r\n",
					"\r",
					"\n"
				), '<br />', preg_replace(array(
					"/\s\s+/",
					"/\r\r+/",
					"/\n\n+/"
				), '<br />', trim(str_replace($find, $replace, $format))));

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);

				$payment_address = str_replace(array(
					"\r\n",
					"\r",
					"\n"
				), '<br />', preg_replace(array(
					"/\s\s+/",
					"/\r\r+/",
					"/\n\n+/"
				), '<br />', trim(str_replace($find, $replace, $format))));

				$product_data = array();

				$products = $this->Model_Sale_Order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->Model_Sale_Order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = substr($option['value'], 0, strrpos($option['value'], '.'));
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$voucher_data = array();

				$vouchers = $this->Model_Sale_Order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data = $this->Model_Sale_Order->getOrderTotals($order_id);

				$data['orders'][] = array(
					'order_id'         => $order_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date('short', strtotime($order_info['date_added'])),
					'store_name'       => $order_info['store_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => nl2br($store_address),
					'store_email'      => $store_email,
					'store_telephone'  => $store_telephone,
					'store_fax'        => $store_fax,
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'payment_address'  => $payment_address,
					'payment_method'   => $order_info['payment_method'],
					'shipping_method'  => $order_info['shipping_method'],
					'product'          => $product_data,
					'voucher'          => $voucher_data,
					'total'            => $total_data,
					'comment'          => nl2br($order_info['comment'])
				);
			}
		}


		$this->response->setOutput($this->render('sale/order_invoice', $data));
	}
}
