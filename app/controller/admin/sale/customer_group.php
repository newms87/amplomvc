<?php
class App_Controller_Admin_Sale_CustomerGroup extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Customer Group"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Customer Group"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_CustomerGroup->addCustomerGroup($_POST);

			$this->message->add('success', _l("Success: You have modified customer groups!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/sale/customer_group', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Customer Group"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_CustomerGroup->editCustomerGroup($_GET['customer_group_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified customer groups!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/sale/customer_group', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Customer Group"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $customer_group_id) {
				$this->Model_Sale_CustomerGroup->deleteCustomerGroup($customer_group_id);
			}

			$this->message->add('success', _l("Success: You have modified customer groups!"));

			$url = '';

			if (isset($_GET['sort'])) {
				$url .= '&sort=' . $_GET['sort'];
			}

			if (isset($_GET['order'])) {
				$url .= '&order=' . $_GET['order'];
			}

			if (isset($_GET['page'])) {
				$url .= '&page=' . $_GET['page'];
			}

			redirect('admin/sale/customer_group', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($_GET['order'])) {
			$order = $_GET['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Customer Group"), site_url('admin/sale/customer_group', $url));

		$data['insert'] = site_url('admin/sale/customer_group/insert', $url);
		$data['delete'] = site_url('admin/sale/customer_group/delete', $url);

		$data['customer_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$customer_group_total = $this->Model_Sale_CustomerGroup->getTotalCustomerGroups();

		$results = $this->Model_Sale_CustomerGroup->getCustomerGroups($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/sale/customer_group/update', 'customer_group_id=' . $result['customer_group_id'] . $url)
			);

			$data['customer_groups'][] = array(
				'customer_group_id' => $result['customer_group_id'],
				'name'              => $result['name'] . (($result['customer_group_id'] == option('config_customer_group_id')) ? _l(" <b>(Default)</b>") : null),
				'selected'          => isset($_GET['selected']) && in_array($result['customer_group_id'], $_GET['selected']),
				'action'            => $action
			);
		}

		if (isset($this->error['warning'])) {
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$data['sort_name'] = site_url('admin/sale/customer_group', 'sort=name' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $customer_group_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('sale/customer_group_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Customer Group"), site_url('admin/sale/customer_group', $url));

		if (!isset($_GET['customer_group_id'])) {
			$data['action'] = site_url('admin/sale/customer_group/insert', $url);
		} else {
			$data['action'] = site_url('admin/sale/customer_group/update', 'customer_group_id=' . $_GET['customer_group_id'] . $url);
		}

		$data['cancel'] = site_url('admin/sale/customer_group', $url);

		if (isset($_GET['customer_group_id']) && !$this->request->isPost()) {
			$customer_group_info = $this->Model_Sale_CustomerGroup->getCustomerGroup($_GET['customer_group_id']);
		}

		if (isset($_POST['name'])) {
			$data['name'] = $_POST['name'];
		} elseif (isset($customer_group_info)) {
			$data['name'] = $customer_group_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->response->setOutput($this->render('sale/customer_group_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/customer_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customer groups!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = _l("Customer Group Name must be between 3 and 64 characters!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/customer_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customer groups!");
		}

		foreach ($_GET['selected'] as $customer_group_id) {
			if (option('config_customer_group_id') == $customer_group_id) {
				$this->error['warning'] = _l("Warning: This customer group cannot be deleted as it is currently assigned as the default store customer group!");
			}

			$store_total = $this->Model_Setting_Store->getTotalStoresByCustomerGroupId($customer_group_id);

			if ($store_total) {
				$this->error['warning'] = sprintf(_l("Warning: This customer group cannot be deleted as it is currently assigned to %s stores!"), $store_total);
			}

			$customer_total = $this->Model_Sale_Customer->getTotalCustomersByCustomerGroupId($customer_group_id);

			if ($customer_total) {
				$this->error['warning'] = sprintf(_l("Warning: This customer group cannot be deleted as it is currently assigned to %s customers!"), $customer_total);
			}
		}

		return empty($this->error);
	}
}
