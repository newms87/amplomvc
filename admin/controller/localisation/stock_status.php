<?php
class Admin_Controller_Localisation_StockStatus extends Controller
{


	public function index()
	{
		$this->language->load('localisation/stock_status');

		$this->document->setTitle($this->_('head_title'));

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('localisation/stock_status');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_StockStatus->addStockStatus($_POST);

			$this->message->add('success', $this->_('text_success'));

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

			$this->url->redirect('localisation/stock_status', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->language->load('localisation/stock_status');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_StockStatus->editStockStatus($_GET['stock_status_id'], $_POST);

			$this->message->add('success', $this->_('text_success'));

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

			$this->url->redirect('localisation/stock_status', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('localisation/stock_status');

		$this->document->setTitle($this->_('head_title'));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $stock_status_id) {
				$this->Model_Localisation_StockStatus->deleteStockStatus($stock_status_id);
			}

			$this->message->add('success', $this->_('text_success'));

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

			$this->url->redirect('localisation/stock_status', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('localisation/stock_status_list');

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

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/stock_status', $url));

		$this->data['insert'] = $this->url->link('localisation/stock_status/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/stock_status/delete', $url);

		$this->data['stock_statuses'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$stock_status_total = $this->Model_Localisation_StockStatus->getTotalStockStatuses();

		$results = $this->Model_Localisation_StockStatus->getStockStatuses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/stock_status/update', 'stock_status_id=' . $result['stock_status_id'] . $url)
			);

			$this->data['stock_statuses'][] = array(
				'stock_status_id' => $result['stock_status_id'],
				'name'            => $result['name'] . (($result['stock_status_id'] == $this->config->get('config_stock_status_id')) ? $this->_('text_default') : null),
				'selected'        => isset($_GET['selected']) && in_array($result['stock_status_id'], $_GET['selected']),
				'action'          => $action
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

		$this->data['sort_name'] = $this->url->link('localisation/stock_status', 'sort=name' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $stock_status_total;
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
		$this->template->load('localisation/stock_status_form');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
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

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('localisation/stock_status', $url));

		if (!isset($_GET['stock_status_id'])) {
			$this->data['action'] = $this->url->link('localisation/stock_status/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/stock_status/update', 'stock_status_id=' . $_GET['stock_status_id'] . $url);
		}

		$this->data['cancel'] = $this->url->link('localisation/stock_status', $url);

		$this->data['languages'] = $this->Model_Localisation_Language->getLanguages();

		if (isset($_POST['stock_status'])) {
			$this->data['stock_status'] = $_POST['stock_status'];
		} elseif (isset($_GET['stock_status_id'])) {
			$this->data['stock_status'] = $this->Model_Localisation_StockStatus->getStockStatusDescriptions($_GET['stock_status_id']);
		} else {
			$this->data['stock_status'] = array();
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'localisation/stock_status')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['stock_status'] as $language_id => $value) {
			if ((strlen($value['name']) < 3) || (strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->_('error_name');
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'localisation/stock_status')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_GET['selected'] as $stock_status_id) {
			if ($this->config->get('config_stock_status_id') == $stock_status_id) {
				$this->error['warning'] = $this->_('error_default');
			}

			$data = array(
				'stock_status_id' => $stock_status_id,
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->_('error_product'), $product_total);
			}
		}

		return $this->error ? false : true;
	}
}
