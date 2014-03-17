<?php

class Admin_Controller_Sale_Customer extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Customer"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Customer"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Customer->addCustomer($_POST);

			$this->message->add('success', _l("Success: You have modified customers!"));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}

			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}

			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}

			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}

			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}

			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('sale/customer', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Customer"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Customer->editCustomer($_GET['customer_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified customers!"));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}

			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}

			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}

			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}

			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}

			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('sale/customer', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Customer"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $customer_id) {
				$this->Model_Sale_Customer->deleteCustomer($customer_id);
			}

			$this->message->add('success', _l("Success: You have modified customers!"));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}

			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}

			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}

			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}

			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}

			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('sale/customer', $url);
		}

		$this->getList();
	}

	public function approve()
	{
		$this->document->setTitle(_l("Customer"));

		if (!$this->user->can('modify', 'sale/customer')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customers!");
		} elseif (isset($_GET['selected'])) {
			$approved = 0;

			foreach ($_GET['selected'] as $customer_id) {
				$customer_info = $this->Model_Sale_Customer->getCustomer($customer_id);

				if ($customer_info && !$customer_info['approved']) {
					$this->Model_Sale_Customer->approve($customer_id);

					$approved++;
				}
			}

			$this->message->add('success', sprintf(_l("You have approved %s accounts!"), $approved));

			$url = '';

			if (isset($_GET['filter_name'])) {
				$url .= '&filter_name=' . $_GET['filter_name'];
			}

			if (isset($_GET['filter_email'])) {
				$url .= '&filter_email=' . $_GET['filter_email'];
			}

			if (isset($_GET['filter_customer_group_id'])) {
				$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
			}

			if (isset($_GET['filter_status'])) {
				$url .= '&filter_status=' . $_GET['filter_status'];
			}

			if (isset($_GET['filter_approved'])) {
				$url .= '&filter_approved=' . $_GET['filter_approved'];
			}

			if (isset($_GET['filter_ip'])) {
				$url .= '&filter_ip=' . $_GET['filter_ip'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			$this->url->redirect('sale/customer', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		//Page Head
		$this->document->setTitle(_l("Customers"));

		//Breadcrumbs
		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Customer"), $this->url->here());

		//The Table Columns
		$columns = array();

		$columns['name'] = array(
			'type'         => 'text',
			'display_name' => _l("Customer"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['email'] = array(
			'type'         => 'text',
			'display_name' => _l("Email"),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['customer_group'] = array(
			'type'         => 'select',
			'display_name' => _l("Customer Group"),
			'build_config' => array('customer_group_id', 'name'),
			'build_data'   => $this->Model_Sale_CustomerGroup->getCustomerGroups(),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['status'] = array(
			'type'         => 'select',
			'display_name' => _l("Status"),
			'build_data'   => array(
				0 => _l("Inactive"),
				1 => _l("Active")
			),
			'filter'       => true,
			'sortable'     => true,
		);

		$columns['date_added'] = array(
			'type'         => 'date',
			'display_name' => _l("Date Added"),
			'filter'       => true,
			'sortable'     => true,
		);

		$this->data['customers'] = array();

		//Sort / Filter
		$sort   = $this->sort->getQueryDefaults('customer_id', 'ASC');
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Data
		$customer_total = $this->Model_Sale_Customer->getTotalCustomers($filter);
		$customers      = $this->Model_Sale_Customer->getCustomers($sort + $filter);

		$query = $this->url->getQueryExclude("customer_id");

		foreach ($customers as &$customer) {
			$customer['actions'] = array(
				'edit'   => array(
					'text' => _l("Edit"),
					'href' => $this->url->link('sale/customer/update', 'customer_id=' . $customer['customer_id'] . '&' . $query)
				),
				'delete' => array(
					'text' => _l("Delete"),
					'href' => $this->url->link('sale/customer/delete', 'customer_id=' . $customer['customer_id'] . '&' . $query)
				),

			);

			$customer['approved'] = $customer['approved'] ? _l("Yes") : _l("No");
		}
		unset($customer);

		//Build The Table
		$tt_data = array(
			'row_id' => 'customer_id',
		);

		$this->table->init();
		$this->table->setTemplate('table/list_view');
		$this->table->setColumns($columns);
		$this->table->setRows($customers);
		$this->table->setTemplateData($tt_data);
		$this->table->mapAttribute('filter_value', $filter);

		$this->data['list_view'] = $this->table->render();

		//Render Limit Menu
		$this->data['limits'] = $this->sort->renderLimits();

		//Pagination
		$this->pagination->init();
		$this->pagination->total = $customer_total;

		$this->data['pagination'] = $this->pagination->render();

		//Actions
		$this->data['approve'] = $this->url->link('sale/customer/approve');
		$this->data['insert']  = $this->url->link('sale/customer/insert');
		$this->data['delete']  = $this->url->link('sale/customer/delete');

		//The Template
		$this->view->load('sale/customer_list');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->view->load('sale/customer_form');

		if (isset($_GET['customer_id'])) {
			$this->data['customer_id'] = $_GET['customer_id'];
		} else {
			$this->data['customer_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}

		if (isset($this->error['address_firstname'])) {
			$this->data['error_address_firstname'] = $this->error['address_firstname'];
		} else {
			$this->data['error_address_firstname'] = '';
		}

		if (isset($this->error['address_lastname'])) {
			$this->data['error_address_lastname'] = $this->error['address_lastname'];
		} else {
			$this->data['error_address_lastname'] = '';
		}

		if (isset($this->error['address_address_1'])) {
			$this->data['error_address_address_1'] = $this->error['address_address_1'];
		} else {
			$this->data['error_address_address_1'] = '';
		}

		if (isset($this->error['address_city'])) {
			$this->data['error_address_city'] = $this->error['address_city'];
		} else {
			$this->data['error_address_city'] = '';
		}

		if (isset($this->error['address_postcode'])) {
			$this->data['error_address_postcode'] = $this->error['address_postcode'];
		} else {
			$this->data['error_address_postcode'] = '';
		}

		if (isset($this->error['address_country'])) {
			$this->data['error_address_country'] = $this->error['address_country'];
		} else {
			$this->data['error_address_country'] = '';
		}

		if (isset($this->error['address_zone'])) {
			$this->data['error_address_zone'] = $this->error['address_zone'];
		} else {
			$this->data['error_address_zone'] = '';
		}

		$url = '';

		if (isset($_GET['filter_name'])) {
			$url .= '&filter_name=' . $_GET['filter_name'];
		}

		if (isset($_GET['filter_email'])) {
			$url .= '&filter_email=' . $_GET['filter_email'];
		}

		if (isset($_GET['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $_GET['filter_customer_group_id'];
		}

		if (isset($_GET['filter_status'])) {
			$url .= '&filter_status=' . $_GET['filter_status'];
		}

		if (isset($_GET['filter_approved'])) {
			$url .= '&filter_approved=' . $_GET['filter_approved'];
		}

		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Customer"), $this->url->link('sale/customer', $url));

		if (!isset($_GET['customer_id'])) {
			$this->data['action'] = $this->url->link('sale/customer/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/customer/update', 'customer_id=' . $_GET['customer_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('sale/customer', $url);

		if (isset($_GET['customer_id']) && !$this->request->isPost()) {
			$customer_info = $this->Model_Sale_Customer->getCustomer($_GET['customer_id']);
		}

		if (isset($_POST['firstname'])) {
			$this->data['firstname'] = $_POST['firstname'];
		} elseif (!empty($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$this->data['lastname'] = $_POST['lastname'];
		} elseif (!empty($customer_info)) {
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
			$this->data['email'] = $_POST['email'];
		} elseif (!empty($customer_info)) {
			$this->data['email'] = $customer_info['email'];
		} else {
			$this->data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
			$this->data['telephone'] = $_POST['telephone'];
		} elseif (!empty($customer_info)) {
			$this->data['telephone'] = $customer_info['telephone'];
		} else {
			$this->data['telephone'] = '';
		}

		if (isset($_POST['fax'])) {
			$this->data['fax'] = $_POST['fax'];
		} elseif (!empty($customer_info)) {
			$this->data['fax'] = $customer_info['fax'];
		} else {
			$this->data['fax'] = '';
		}

		if (isset($_POST['newsletter'])) {
			$this->data['newsletter'] = $_POST['newsletter'];
		} elseif (!empty($customer_info)) {
			$this->data['newsletter'] = $customer_info['newsletter'];
		} else {
			$this->data['newsletter'] = '';
		}

		$this->data['customer_groups'] = $this->Model_Sale_CustomerGroup->getCustomerGroups();

		if (isset($_POST['customer_group_id'])) {
			$this->data['customer_group_id'] = $_POST['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$this->data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($_POST['status'])) {
			$this->data['status'] = $_POST['status'];
		} elseif (!empty($customer_info)) {
			$this->data['status'] = $customer_info['status'];
		} else {
			$this->data['status'] = 1;
		}

		if (isset($_POST['password'])) {
			$this->data['password'] = $_POST['password'];
		} else {
			$this->data['password'] = '';
		}

		if (isset($_POST['confirm'])) {
			$this->data['confirm'] = $_POST['confirm'];
		} else {
			$this->data['confirm'] = '';
		}

		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();

		if (isset($_POST['address'])) {
			$this->data['addresses'] = $_POST['address'];
		} elseif (!empty($_GET['customer_id'])) {
			$this->data['addresses'] = $this->Model_Sale_Customer->getAddresses($_GET['customer_id']);
		} else {
			$this->data['addresses'] = array();
		}

		if (isset($_POST['address_id'])) {
			$this->data['address_id'] = $_POST['address_id'];
		} elseif (!empty($customer_info)) {
			$this->data['address_id'] = $customer_info['address_id'];
		} else {
			$this->data['address_id'] = '';
		}

		$this->data['ips'] = array();

		if (!empty($customer_info)) {
			$results = $this->Model_Sale_Customer->getIpsByCustomerId($_GET['customer_id']);

			foreach ($results as $result) {
				$blacklist_total = $this->Model_Sale_Customer->getTotalBlacklistsByIp($result['ip']);

				$this->data['ips'][] = array(
					'ip'         => $result['ip'],
					'total'      => $this->Model_Sale_Customer->getTotalCustomersByIp($result['ip']),
					'date_added' => date('d/m/y', strtotime($result['date_added'])),
					'filter_ip'  => $this->url->link('sale/customer', 'filter_ip=' . $result['ip']),
					'blacklist'  => $blacklist_total
				);
			}
		}

		//Ajax Urls
		$this->data['url_transaction']      = $this->url->link('sale/customer/transaction', 'customer_id=' . (int)$customer_id);
		$this->data['url_reward']           = $this->url->link('sale/customer/reward', 'customer_id=' . (int)$customer_id);
		$this->data['url_blacklist']        = $this->url->link('sale/customer/addblacklist');
		$this->data['url_remove_blacklist'] = $this->url->link('sale/customer/removeblacklist');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/customer')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customers!");
		}

		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
			$this->error['email'] = _l("E-Mail Address does not appear to be valid!");
		}

		$customer_info = $this->Model_Sale_Customer->getCustomerByEmail($_POST['email']);

		if (!isset($_GET['customer_id'])) {
			if ($customer_info) {
				$this->error['warning'] = _l("Warning: E-Mail Address is already registered!");
			}
		} else {
			if ($customer_info && ($_GET['customer_id'] != $customer_info['customer_id'])) {
				$this->error['warning'] = _l("Warning: E-Mail Address is already registered!");
			}
		}

		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
			$this->error['telephone'] = _l("Telephone must be between 3 and 32 characters!");
		}

		if ($_POST['password'] || (!isset($_GET['customer_id']))) {
			if ((strlen($_POST['password']) < 4) || (strlen($_POST['password']) > 20)) {
				$this->error['password'] = _l("Password must be between 4 and 20 characters!");
			}

			if ($_POST['password'] != $_POST['confirm']) {
				$this->error['confirm'] = _l("Password and password confirmation do not match!");
			}
		}

		if (isset($_POST['address'])) {
			foreach ($_POST['address'] as $key => $value) {
				if ((strlen($value['firstname']) < 1) || (strlen($value['firstname']) > 32)) {
					$this->error['address_firstname'][$key] = _l("First Name must be between 1 and 32 characters!");
				}

				if ((strlen($value['lastname']) < 1) || (strlen($value['lastname']) > 32)) {
					$this->error['address_lastname'][$key] = _l("Last Name must be between 1 and 32 characters!");
				}

				if ((strlen($value['address_1']) < 3) || (strlen($value['address_1']) > 128)) {
					$this->error['address_address_1'][$key] = _l("Address 1 must be between 3 and 128 characters!");
				}

				if ((strlen($value['city']) < 2) || (strlen($value['city']) > 128)) {
					$this->error['address_city'][$key] = _l("City must be between 2 and 128 characters!");
				}

				$country_info = $this->Model_Localisation_Country->getCountry($value['country_id']);

				if ($country_info && $country_info['postcode_required'] && (strlen($value['postcode']) < 2) || (strlen($value['postcode']) > 10)) {
					$this->error['address_postcode'][$key] = _l("Postcode must be between 2 and 10 characters for this country!");
				}

				if ($value['country_id'] == '') {
					$this->error['address_country'][$key] = _l("Please select a country!");
				}

				if ($value['zone_id'] == '') {
					$this->error['address_zone'][$key] = _l("Please select a region / state!");
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = _l("Warning: Please check the form carefully for errors!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/customer')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customers!");
		}

		return $this->error ? false : true;
	}

	public function login()
	{
		$json = array();

		if (isset($_GET['customer_id'])) {
			$customer_id = $_GET['customer_id'];
		} else {
			$customer_id = 0;
		}

		$customer_info = $this->Model_Sale_Customer->getCustomer($customer_id);

		if ($customer_info) {
			$token = md5(mt_rand());

			$this->Model_Sale_Customer->editToken($customer_id, $token);

			if (isset($_GET['store_id'])) {
				$store_id = $_GET['store_id'];
			} else {
				$store_id = 0;
			}

			$store_info = $this->Model_Setting_Store->getStore($store_id);

			if ($store_info) {
				$this->url->redirect($this->url->store($store_id, 'account/login'));
			} else {
				$this->url->redirect($this->url->store($this->config->get('config_default_store'), 'account/login'));
			}
		} else {
			$this->view->load('error/not_found');

			$this->document->setTitle(_l("Customer"));

			$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
			$this->breadcrumb->add(_l("Customer"), $this->url->link('error/not_found'));

			$this->children = array(
				'common/header',
				'common/footer'
			);

			$this->response->setOutput($this->render());
		}
	}

	public function transaction()
	{
		$this->view->load('sale/customer_transaction');

		if ($this->request->isPost() && $this->user->can('modify', 'sale/customer')) {
			$this->Model_Sale_Customer->addTransaction($_GET['customer_id'], $_POST['description'], $_POST['amount']);

			$this->message->add('success', _l("Success: You have modified customers!"));
		}

		if ($this->request->isPost() && !$this->user->can('modify', 'sale/customer')) {
			$this->message->add('warning', _l("Warning: You do not have permission to modify customers!"));
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$this->data['transactions'] = array();

		$results = $this->Model_Sale_Customer->getTransactions($_GET['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], 'short'),
			);
		}

		$this->data['balance'] = $this->currency->format($this->Model_Sale_Customer->getTransactionTotal($_GET['customer_id']), $this->config->get('config_currency'));

		$transaction_total = $this->Model_Sale_Customer->getTotalTransactions($_GET['customer_id']);

		$this->pagination->init();
		$this->pagination->total  = $transaction_total;
		$this->data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render());
	}

	public function reward()
	{
		$this->view->load('sale/customer_reward');

		if ($this->request->isPost() && $this->user->can('modify', 'sale/customer')) {
			$this->Model_Sale_Customer->addReward($_GET['customer_id'], $_POST['description'], $_POST['points']);

			$this->message->add('success', _l("Success: You have modified customers!"));
		}

		if ($this->request->isPost() && !$this->user->can('modify', 'sale/customer')) {
			$this->message->add('warning', _l("Warning: You do not have permission to modify customers!"));
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$this->data['rewards'] = array();

		$results = $this->Model_Sale_Customer->getRewards($_GET['customer_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => $this->date->format($result['date_added'], 'short'),
			);
		}

		$this->data['balance'] = $this->Model_Sale_Customer->getRewardTotal($_GET['customer_id']);

		$reward_total = $this->Model_Sale_Customer->getTotalRewards($_GET['customer_id']);

		$this->pagination->init();
		$this->pagination->total  = $reward_total;
		$this->data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render());
	}

	public function addblacklist()
	{
		$json = array();

		if (isset($_POST['ip'])) {
			if (!$this->user->can('modify', 'sale/customer')) {
				$json['error'] = _l("Warning: You do not have permission to modify customers!");
			} else {
				$this->Model_Sale_Customer->addBlacklist($_POST['ip']);

				$json['success'] = _l("Success: You have modified customers!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function removeblacklist()
	{
		$json = array();

		if (isset($_POST['ip'])) {
			if (!$this->user->can('modify', 'sale/customer')) {
				$json['error'] = _l("Warning: You do not have permission to modify customers!");
			} else {
				$this->Model_Sale_Customer->deleteBlacklist($_POST['ip']);

				$json['success'] = _l("Success: You have modified customers!");
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete()
	{
		//Sort
		$sort = $this->sort->getQueryDefaults('name', 'ASC', $this->config->get('config_autocomplete_limit'));

		//Filter
		$filter = !empty($_GET['filter']) ? $_GET['filter'] : array();

		//Label and Value
		$label = !empty($_GET['label']) ? $_GET['label'] : 'name';
		$value = !empty($_GET['value']) ? $_GET['value'] : 'customer_id';

		//Load Sorted / Filtered Data
		$customers = $this->Model_Sale_Customer->getCustomers($sort + $filter);

		foreach ($customers as &$customer) {
			$customer['label'] = $customer[$label];
			$customer['value'] = $customer[$value];

			$customer['name']    = html_entity_decode($customer['name'], ENT_QUOTES, 'UTF-8');
			$customer['address'] = $this->Model_Sale_Customer->getAddresses($customer['customer_id']);
		}
		unset($customer);

		//JSON output
		$this->response->setOutput(json_encode($customers));
	}

	public function address()
	{
		$json = array();

		if (!empty($_GET['address_id'])) {
			$json = $this->Model_Sale_Customer->getAddress($_GET['address_id']);
		}

		$this->response->setOutput(json_encode($json));
	}
}
