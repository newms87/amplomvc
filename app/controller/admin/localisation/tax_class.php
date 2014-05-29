<?php
class App_Controller_Admin_Localisation_TaxClass extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Tax Class"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Tax Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Taxclass->addTaxClass($_POST);

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_class', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Tax Class"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Taxclass->editTaxClass($_GET['tax_class_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_class', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Tax Class"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $tax_class_id) {
				$this->Model_Localisation_Taxclass->deleteTaxClass($tax_class_id);
			}

			$this->message->add('success', _l("Success: You have modified tax classes!"));

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

			redirect('admin/localisation/tax_class', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'title';
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
		$this->breadcrumb->add(_l("Tax Class"), site_url('admin/localisation/tax_class', $url));

		$data['insert'] = site_url('admin/localisation/tax_class/insert', $url);
		$data['delete'] = site_url('admin/localisation/tax_class/delete', $url);

		$data['tax_classes'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$tax_class_total = $this->Model_Localisation_TaxClass->getTotalTaxClasses();

		$results = $this->Model_Localisation_TaxClass->getTaxClasses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/tax_class/update', 'tax_class_id=' . $result['tax_class_id'] . $url)
			);

			$data['tax_classes'][] = array(
				'tax_class_id' => $result['tax_class_id'],
				'title'        => $result['title'],
				'selected'     => isset($_GET['selected']) && in_array($result['tax_class_id'], $_GET['selected']),
				'action'       => $action
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

		$data['sort_title'] = site_url('admin/localisation/tax_class', 'sort=title' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $tax_class_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/tax_class_list', $data));
	}

	private function getForm()
	{
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
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
		$this->breadcrumb->add(_l("Tax Class"), site_url('admin/localisation/tax_class', $url));

		if (!isset($_GET['tax_class_id'])) {
			$data['action'] = site_url('admin/localisation/tax_class/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/tax_class/update', 'tax_class_id=' . $_GET['tax_class_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/tax_class', $url);

		if (isset($_GET['tax_class_id']) && !$this->request->isPost()) {
			$tax_class_info = $this->Model_Localisation_TaxClass->getTaxClass($_GET['tax_class_id']);
		}

		if (isset($_POST['title'])) {
			$data['title'] = $_POST['title'];
		} elseif (isset($tax_class_info)) {
			$data['title'] = $tax_class_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($_POST['description'])) {
			$data['description'] = $_POST['description'];
		} elseif (isset($tax_class_info)) {
			$data['description'] = $tax_class_info['description'];
		} else {
			$data['description'] = '';
		}

		$data['tax_rates'] = $this->Model_Localisation_TaxRate->getTaxRates();

		if (isset($_POST['tax_rule'])) {
			$data['tax_rules'] = $_POST['tax_rule'];
		} elseif (isset($_GET['tax_class_id'])) {
			$data['tax_rules'] = $this->Model_Localisation_Taxclass->getTaxRules($_GET['tax_class_id']);
		} else {
			$data['tax_rules'] = array();
		}

		$this->response->setOutput($this->render('localisation/tax_class_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/tax_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify tax classes!");
		}

		if ((strlen($_POST['title']) < 3) || (strlen($_POST['title']) > 32)) {
			$this->error['title'] = _l("Tax Class Title must be between 3 and 32 characters!");
		}

		if ((strlen($_POST['description']) < 3) || (strlen($_POST['description']) > 255)) {
			$this->error['description'] = _l("Description must be between 3 and 255 characters!");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/tax_class')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify tax classes!");
		}

		foreach ($_GET['selected'] as $tax_class_id) {
			$data = array(
				'tax_class_id' => $tax_class_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf(_l("Warning: This tax class cannot be deleted as it is currently assigned to %s products!"), $product_total);
			}
		}

		return empty($this->error);
	}
}
