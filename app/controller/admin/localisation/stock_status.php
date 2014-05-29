<?php
class App_Controller_Admin_Localisation_StockStatus extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Stock Status"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Stock Status"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_StockStatus->addStockStatus($_POST);

			$this->message->add('success', _l("Success: You have modified stock statuses!"));

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

			redirect('admin/localisation/stock_status', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Stock Status"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_StockStatus->editStockStatus($_GET['stock_status_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified stock statuses!"));

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

			redirect('admin/localisation/stock_status', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Stock Status"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $stock_status_id) {
				$this->Model_Localisation_StockStatus->deleteStockStatus($stock_status_id);
			}

			$this->message->add('success', _l("Success: You have modified stock statuses!"));

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

			redirect('admin/localisation/stock_status', $url);
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
		$this->breadcrumb->add(_l("Stock Status"), site_url('admin/localisation/stock_status', $url));

		$data['insert'] = site_url('admin/localisation/stock_status/insert', $url);
		$data['delete'] = site_url('admin/localisation/stock_status/delete', $url);

		$data['stock_statuses'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * option('config_admin_limit'),
			'limit' => option('config_admin_limit')
		);

		$stock_status_total = $this->Model_Localisation_StockStatus->getTotalStockStatuses();

		$results = $this->Model_Localisation_StockStatus->getStockStatuses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => site_url('admin/localisation/stock_status/update', 'stock_status_id=' . $result['stock_status_id'] . $url)
			);

			$data['stock_statuses'][] = array(
				'stock_status_id' => $result['stock_status_id'],
				'name'            => $result['name'] . (($result['stock_status_id'] == option('config_stock_status_id')) ? _l(" <b>(Default)</b>") : null),
				'selected'        => isset($_GET['selected']) && in_array($result['stock_status_id'], $_GET['selected']),
				'action'          => $action
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

		$data['sort_name'] = site_url('admin/localisation/stock_status', 'sort=name' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $stock_status_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/stock_status_list', $data));
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
			$data['error_name'] = array();
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
		$this->breadcrumb->add(_l("Stock Status"), site_url('admin/localisation/stock_status', $url));

		if (!isset($_GET['stock_status_id'])) {
			$data['action'] = site_url('admin/localisation/stock_status/insert', $url);
		} else {
			$data['action'] = site_url('admin/localisation/stock_status/update', 'stock_status_id=' . $_GET['stock_status_id'] . $url);
		}

		$data['cancel'] = site_url('admin/localisation/stock_status', $url);

		$data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['stock_status'])) {
			$data['stock_status'] = $_POST['stock_status'];
		} elseif (isset($_GET['stock_status_id'])) {
			$data['stock_status'] = $this->Model_Localisation_StockStatus->getStockStatusDescriptions($_GET['stock_status_id']);
		} else {
			$data['stock_status'] = array();
		}

		$this->response->setOutput($this->render('localisation/stock_status_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/stock_status')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify stock statuses!");
		}

		foreach ($_POST['stock_status'] as $language_id => $value) {
			if ((strlen($value['name']) < 3) || (strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = _l("Stock Status Name must be between 3 and 32 characters!");
			}
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/stock_status')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify stock statuses!");
		}

		foreach ($_GET['selected'] as $stock_status_id) {
			if (option('config_stock_status_id') == $stock_status_id) {
				$this->error['warning'] = _l("You cannot delete the default Stock Status");
			}

			$data = array(
				'stock_status_id' => $stock_status_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf(_l("Warning: This stock status cannot be deleted as it is currently assigned to %s products!"), $product_total);
			}
		}

		return empty($this->error);
	}
}
