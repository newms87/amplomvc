<?php
class Admin_Controller_Sale_Return extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Product Returns"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Product Returns"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Return->addReturn($_POST);

			$this->message->add('success', _l("Success: You have modified returns!"));

			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}

			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}

			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}

			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}

			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			redirect('sale/return', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Product Returns"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Return->editReturn($_GET['return_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified returns!"));

			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}

			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}

			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}

			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}

			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			redirect('sale/return', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Product Returns"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $return_id) {
				$this->Model_Sale_Return->deleteReturn($return_id);
			}

			$this->message->add('success', _l("Success: You have modified returns!"));

			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}

			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}

			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}

			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}

			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			redirect('sale/return', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['filter_return_id'])) {
			$filter_return_id = $_GET['filter_return_id'];
		} else {
			$filter_return_id = null;
		}

		if (isset($_GET['filter_order_id'])) {
			$filter_order_id = $_GET['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($_GET['filter_customer'])) {
			$filter_customer = $_GET['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($_GET['filter_product'])) {
			$filter_product = $_GET['filter_product'];
		} else {
			$filter_product = null;
		}

		if (isset($_GET['filter_model'])) {
			$filter_model = $_GET['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($_GET['filter_return_status_id'])) {
			$filter_return_status_id = $_GET['filter_return_status_id'];
		} else {
			$filter_return_status_id = null;
		}

		if (isset($_GET['filter_date_added'])) {
			$filter_date_added = $_GET['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($_GET['filter_date_modified'])) {
			$filter_date_modified = $_GET['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'r.return_id';
		}

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}

		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}

		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}

		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}

		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}

		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Product Returns"), site_url('sale/return', $url));

		$data['insert'] = site_url('sale/return/insert', $url);
		$data['delete'] = site_url('sale/return/delete', $url);

		$data['returns'] = array();

		$data = array(
			'filter_return_id'        => $filter_return_id,
			'filter_order_id'         => $filter_order_id,
			'filter_customer'         => $filter_customer,
			'filter_product'          => $filter_product,
			'filter_model'            => $filter_model,
			'filter_return_status_id' => $filter_return_status_id,
			'filter_date_added'       => $filter_date_added,
			'filter_date_modified'    => $filter_date_modified,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ($page - 1) * option('config_admin_limit'),
			'limit'                   => option('config_admin_limit')
		);

		$return_total = $this->Model_Sale_Return->getTotalReturns($data);

		$results = $this->Model_Sale_Return->getReturns($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("View"),
				'href' => site_url('sale/return/info', 'return_id=' . $result['return_id'] . $url)
			);

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('sale/return/update', 'return_id=' . $result['return_id'] . $url)
			);

			$data['returns'][] = array(
				'return_id'     => $result['return_id'],
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'product'       => $result['product'],
				'model'         => $result['model'],
				'status'        => $result['status'],
				'date_added'    => $this->date->format($result['date_added'], 'short'),
				'date_modified' => date('short', strtotime($result['date_modified'])),
				'selected'      => isset($_GET['selected']) && in_array($result['return_id'], $_GET['selected']),
				'action'        => $action
			);
		}

		if ($this->session->has('error')) {
			$data['error_warning'] = $this->session->get('error');

			$this->session->remove('error');
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->remove('success');
		} else {
			$data['success'] = '';
		}

		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}

		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}

		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}

		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}

		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}

		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$data['sort_return_id']     = site_url('sale/return', 'sort=r.return_id' . $url);
		$data['sort_order_id']      = site_url('sale/return', 'sort=r.order_id' . $url);
		$data['sort_customer']      = site_url('sale/return', 'sort=customer' . $url);
		$data['sort_product']       = site_url('sale/return', 'sort=product' . $url);
		$data['sort_model']         = site_url('sale/return', 'sort=model' . $url);
		$data['sort_status']        = site_url('sale/return', 'sort=status' . $url);
		$data['sort_date_added']    = site_url('sale/return', 'sort=r.date_added' . $url);
		$data['sort_date_modified'] = site_url('sale/return', 'sort=r.date_modified' . $url);

		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}

		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}

		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}

		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}

		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}

		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
		}

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $return_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_return_id']        = $filter_return_id;
		$data['filter_order_id']         = $filter_order_id;
		$data['filter_customer']         = $filter_customer;
		$data['filter_product']          = $filter_product;
		$data['filter_model']            = $filter_model;
		$data['filter_return_status_id'] = $filter_return_status_id;
		$data['filter_date_added']       = $filter_date_added;
		$data['filter_date_modified']    = $filter_date_modified;

		$data['data_return_statuses'] = $this->order->getReturnStatuses();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('sale/return_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['order_id'])) {
			$data['error_order_id'] = $this->error['order_id'];
		} else {
			$data['error_order_id'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		$url = '';

		if (isset($_GET['filter_return_id'])) {
			$url .= '&filter_return_id=' . $_GET['filter_return_id'];
		}

		if (isset($_GET['filter_order_id'])) {
			$url .= '&filter_order_id=' . $_GET['filter_order_id'];
		}

		if (isset($_GET['filter_customer'])) {
			$url .= '&filter_customer=' . $_GET['filter_customer'];
		}

		if (isset($_GET['filter_product'])) {
			$url .= '&filter_product=' . $_GET['filter_product'];
		}

		if (isset($_GET['filter_model'])) {
			$url .= '&filter_model=' . $_GET['filter_model'];
		}

		if (isset($_GET['filter_return_status_id'])) {
			$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
		}

		if (isset($_GET['filter_date_added'])) {
			$url .= '&filter_date_added=' . $_GET['filter_date_added'];
		}

		if (isset($_GET['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Product Returns"), site_url('sale/return', $url));

		if (!isset($_GET['return_id'])) {
			$data['action'] = site_url('sale/return/insert', $url);
		} else {
			$data['action'] = site_url('sale/return/update', 'return_id=' . $_GET['return_id'] . $url);
		}

		$data['cancel'] = site_url('sale/return', $url);

		if (isset($_GET['return_id']) && !$this->request->isPost()) {
			$return_info = $this->Model_Sale_Return->getReturn($_GET['return_id']);
		}

		if (isset($_POST['order_id'])) {
			$data['order_id'] = $_POST['order_id'];
		} elseif (!empty($return_info)) {
			$data['order_id'] = $return_info['order_id'];
		} else {
			$data['order_id'] = '';
		}

		if (isset($_POST['date_ordered'])) {
			$data['date_ordered'] = $_POST['date_ordered'];
		} elseif (!empty($return_info)) {
			$data['date_ordered'] = $return_info['date_ordered'];
		} else {
			$data['date_ordered'] = '';
		}

		if (isset($_POST['customer'])) {
			$data['customer'] = $_POST['customer'];
		} elseif (!empty($return_info)) {
			$data['customer'] = $return_info['customer'];
		} else {
			$data['customer'] = '';
		}

		if (isset($_POST['customer_id'])) {
			$data['customer_id'] = $_POST['customer_id'];
		} elseif (!empty($return_info)) {
			$data['customer_id'] = $return_info['customer_id'];
		} else {
			$data['customer_id'] = '';
		}

		if (isset($_POST['firstname'])) {
			$data['firstname'] = $_POST['firstname'];
		} elseif (!empty($return_info)) {
			$data['firstname'] = $return_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($_POST['lastname'])) {
			$data['lastname'] = $_POST['lastname'];
		} elseif (!empty($return_info)) {
			$data['lastname'] = $return_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($_POST['email'])) {
			$data['email'] = $_POST['email'];
		} elseif (!empty($return_info)) {
			$data['email'] = $return_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($_POST['telephone'])) {
			$data['telephone'] = $_POST['telephone'];
		} elseif (!empty($return_info)) {
			$data['telephone'] = $return_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($_POST['product'])) {
			$data['product'] = $_POST['product'];
		} elseif (!empty($return_info)) {
			$data['product'] = $return_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($_POST['product_id'])) {
			$data['product_id'] = $_POST['product_id'];
		} elseif (!empty($return_info)) {
			$data['product_id'] = $return_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($_POST['model'])) {
			$data['model'] = $_POST['model'];
		} elseif (!empty($return_info)) {
			$data['model'] = $return_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($_POST['quantity'])) {
			$data['quantity'] = $_POST['quantity'];
		} elseif (!empty($return_info)) {
			$data['quantity'] = $return_info['quantity'];
		} else {
			$data['quantity'] = '';
		}

		if (isset($_POST['opened'])) {
			$data['opened'] = $_POST['opened'];
		} elseif (!empty($return_info)) {
			$data['opened'] = $return_info['opened'];
		} else {
			$data['opened'] = '';
		}

		if (isset($_POST['return_reason_id'])) {
			$data['return_reason_id'] = $_POST['return_reason_id'];
		} elseif (!empty($return_info)) {
			$data['return_reason_id'] = $return_info['return_reason_id'];
		} else {
			$data['return_reason_id'] = '';
		}

		$data['return_reasons'] = $this->Model_Localisation_ReturnReason->getReturnReasons();

		if (isset($_POST['return_action_id'])) {
			$data['return_action_id'] = $_POST['return_action_id'];
		} elseif (!empty($return_info)) {
			$data['return_action_id'] = $return_info['return_action_id'];
		} else {
			$data['return_action_id'] = '';
		}

		$data['return_actions'] = $this->Model_Localisation_ReturnAction->getReturnActions();

		if (isset($_POST['comment'])) {
			$data['comment'] = $_POST['comment'];
		} elseif (!empty($return_info)) {
			$data['comment'] = $return_info['comment'];
		} else {
			$data['comment'] = '';
		}

		if (isset($_POST['return_status_id'])) {
			$data['return_status_id'] = $_POST['return_status_id'];
		} elseif (!empty($return_info)) {
			$data['return_status_id'] = $return_info['return_status_id'];
		} else {
			$data['return_status_id'] = '';
		}

		$data['data_return_statuses'] = $this->order->getReturnStatuses();

		$this->response->setOutput($this->render('sale/return_form', $data));
	}

	public function info()
	{
		if (isset($_GET['return_id'])) {
			$return_id = $_GET['return_id'];
		} else {
			$return_id = 0;
		}

		$return_info = $this->Model_Sale_Return->getReturn($return_id);

		if ($return_info) {
			$this->document->setTitle(_l("Product Returns"));

			$url = '';

			if (isset($_GET['filter_return_id'])) {
				$url .= '&filter_return_id=' . $_GET['filter_return_id'];
			}

			if (isset($_GET['filter_order_id'])) {
				$url .= '&filter_order_id=' . $_GET['filter_order_id'];
			}

			if (isset($_GET['filter_customer'])) {
				$url .= '&filter_customer=' . $_GET['filter_customer'];
			}

			if (isset($_GET['filter_product'])) {
				$url .= '&filter_product=' . $_GET['filter_product'];
			}

			if (isset($_GET['filter_model'])) {
				$url .= '&filter_model=' . $_GET['filter_model'];
			}

			if (isset($_GET['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $_GET['filter_return_status_id'];
			}

			if (isset($_GET['filter_date_added'])) {
				$url .= '&filter_date_added=' . $_GET['filter_date_added'];
			}

			if (isset($_GET['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $_GET['filter_date_modified'];
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

			$this->breadcrumb->add(_l("Home"), site_url('common/home'));
			$this->breadcrumb->add(_l("Product Returns"), site_url('sale/return', $url));

			$data['cancel'] = site_url('sale/return', $url);

			$data = $return_info;

			$order_info = $this->order->get($return_info['order_id']);

			if ($return_info['order_id'] && $order_info) {
				$data['order'] = site_url('sale/order/info', 'order_id=' . $return_info['order_id']);
			} else {
				$data['order'] = '';
			}

			$data['date_ordered'] = date('short', strtotime($return_info['date_ordered']));

			if ($return_info['customer_id']) {
				$data['customer'] = site_url('sale/customer/update', 'customer_id=' . $return_info['customer_id']);
			} else {
				$data['customer'] = '';
			}

			$data['data_return_statuses'] = $this->order->getReturnStatuses();
			$data['data_return_reasons']  = $this->order->getReturnReasons();
			$data['data_return_actions']  = $this->order->getReturnActions();

			$data['date_added']    = date('short', strtotime($return_info['date_added']));
			$data['date_modified'] = date('short', strtotime($return_info['date_modified']));

			$data['opened'] = $return_info['opened'] ? _l("Yes") : _l("No");

			$data['comment'] = nl2br($return_info['comment']);

			$this->response->setOutput($this->render('sale/return_info', $data));
		} else {
			$this->document->setTitle(_l("Product Returns"));

			$this->breadcrumb->add(_l("Home"), site_url('common/home'));
			$this->breadcrumb->add(_l("Product Returns"), site_url('error/not_found'));

			$this->response->setOutput($this->render('error/not_found', $data));
		}
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/return')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify returns!");
		}

		if (!$this->validation->text($_POST['firstname'], 1, 32)) {
			$this->error['firstname'] = _l("First Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->text($_POST['lastname'], 1, 32)) {
			$this->error['lastname'] = _l("Last Name must be between 1 and 32 characters!");
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = $this->validation->getError();
		}

		if ($this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = $this->validation->getError();
		}

		if (!$this->validation->text($_POST['product'], 3, 255)) {
			$this->error['product'] = _l("Product Name must be greater than 3 and less than 255 characters!");
		}

		if (!$this->validation->text($_POST['model'], 3, 64)) {
			$this->error['model'] = _l("Product Model must be greater than 3 and less than 64 characters!");
		}

		if (empty($_POST['return_reason_id'])) {
			$this->error['reason'] = _l("You must specify a return reason!");
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = _l("Warning: Please check the form carefully for errors!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/return')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify returns!");
		}

		return empty($this->error);
	}

	public function action()
	{
		$json = array();

		if ($this->request->isPost()) {

			if (!$this->user->can('modify', 'sale/return')) {
				$json['error'] = _l("Warning: You do not have permission to modify returns!");
			}

			if (!$json) {

				$json['success'] = _l("Success: You have modified returns!");

				$this->Model_Sale_Return->editReturnAction($_GET['return_id'], $_POST['return_action_id']);
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function history()
	{
		if ($this->request->isPost() && $this->user->can('modify', 'sale/return')) {
			$this->Model_Sale_Return->addReturnHistory($_GET['return_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified returns!"));
		}

		if ($this->request->isPost() && !$this->user->can('modify', 'sale/return')) {
			$this->message->add('warning', _l("Warning: You do not have permission to modify returns!"));
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->Model_Sale_Return->getReturnHistories($_GET['return_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? _l("Yes") : _l("No"),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => $this->date->format($result['date_added'], 'short'),
			);
		}

		$history_total = $this->Model_Sale_Return->getTotalReturnHistories($_GET['return_id']);

		$this->pagination->init();
		$this->pagination->total  = $history_total;
		$data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render('sale/return_history', $data));
	}
}
