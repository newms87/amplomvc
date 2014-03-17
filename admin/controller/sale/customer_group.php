<?php
class Admin_Controller_Sale_CustomerGroup extends Controller
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

			$this->url->redirect('sale/customer_group', $url);
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

			$this->url->redirect('sale/customer_group', $url);
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

			$this->url->redirect('sale/customer_group', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$this->view->load('sale/customer_group_list');

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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Customer Group"), $this->url->link('sale/customer_group', $url));

		$this->data['insert'] = $this->url->link('sale/customer_group/insert', $url);
		$this->data['delete'] = $this->url->link('sale/customer_group/delete', $url);

		$this->data['customer_groups'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$customer_group_total = $this->Model_Sale_CustomerGroup->getTotalCustomerGroups();

		$results = $this->Model_Sale_CustomerGroup->getCustomerGroups($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('sale/customer_group/update', 'customer_group_id=' . $result['customer_group_id'] . $url)
			);

			$this->data['customer_groups'][] = array(
				'customer_group_id' => $result['customer_group_id'],
				'name'              => $result['name'] . (($result['customer_group_id'] == $this->config->get('config_customer_group_id')) ? _l(" <b>(Default)</b>") : null),
				'selected'          => isset($_GET['selected']) && in_array($result['customer_group_id'], $_GET['selected']),
				'action'            => $action
			);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
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

		$this->data['sort_name'] = $this->url->link('sale/customer_group', 'sort=name' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $customer_group_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->view->load('sale/customer_group_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Customer Group"), $this->url->link('sale/customer_group', $url));

		if (!isset($_GET['customer_group_id'])) {
			$this->data['action'] = $this->url->link('sale/customer_group/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/customer_group/update', 'customer_group_id=' . $_GET['customer_group_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('sale/customer_group', $url);

		if (isset($_GET['customer_group_id']) && !$this->request->isPost()) {
			$customer_group_info = $this->Model_Sale_CustomerGroup->getCustomerGroup($_GET['customer_group_id']);
		}

		if (isset($_POST['name'])) {
			$this->data['name'] = $_POST['name'];
		} elseif (isset($customer_group_info)) {
			$this->data['name'] = $customer_group_info['name'];
		} else {
			$this->data['name'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/customer_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customer groups!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 64)) {
			$this->error['name'] = _l("Customer Group Name must be between 3 and 64 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/customer_group')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify customer groups!");
		}

		foreach ($_GET['selected'] as $customer_group_id) {
			if ($this->config->get('config_customer_group_id') == $customer_group_id) {
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

		return $this->error ? false : true;
	}
}
