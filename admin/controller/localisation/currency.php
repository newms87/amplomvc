<?php
class Admin_Controller_Localisation_Currency extends Controller
{


	public function index()
	{
		$this->document->setTitle(_l("Currency"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Currency"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Currency->addCurrency($_POST);

			$this->message->add('success', _l("Success: You have modified currencies!"));

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

			$this->url->redirect('localisation/currency', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Currency"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Currency->editCurrency($_GET['currency_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified currencies!"));

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

			$this->url->redirect('localisation/currency', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Currency"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $currency_id) {
				$this->Model_Localisation_Currency->deleteCurrency($currency_id);
			}

			$this->message->add('success', _l("Success: You have modified currencies!"));

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

			$this->url->redirect('localisation/currency', $url);
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

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Currency"), $this->url->link('localisation/currency', $url));

		$data['insert'] = $this->url->link('localisation/currency/insert', $url);
		$data['delete'] = $this->url->link('localisation/currency/delete', $url);

		$data['currencies'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$currency_total = $this->Model_Localisation_Currency->getTotalCurrencies();

		$results = $this->Model_Localisation_Currency->getCurrencies($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('localisation/currency/update', 'currency_id=' . $result['currency_id'] . $url)
			);

			$data['currencies'][] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'] . (($result['code'] == $this->config->get('config_currency')) ? _l(" <b>(Default)</b>") : null),
				'code'          => $result['code'],
				'value'         => $result['value'],
				'date_modified' => $this->date->format($result['date_modified'], 'short'),
				'selected'      => isset($_GET['selected']) && in_array($result['currency_id'], $_GET['selected']),
				'action'        => $action
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
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

		$data['sort_title']         = $this->url->link('localisation/currency', 'sort=title' . $url);
		$data['sort_code']          = $this->url->link('localisation/currency', 'sort=code' . $url);
		$data['sort_value']         = $this->url->link('localisation/currency', 'sort=value' . $url);
		$data['sort_date_modified'] = $this->url->link('localisation/currency', 'sort=date_modified' . $url);

		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total  = $currency_total;
		$data['pagination'] = $this->pagination->render();

		$data['sort']  = $sort;
		$data['order'] = $order;

		$this->response->setOutput($this->render('localisation/currency_list', $data));
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

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
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
		$this->breadcrumb->add(_l("Currency"), $this->url->link('localisation/currency', $url));

		if (!isset($_GET['currency_id'])) {
			$data['action'] = $this->url->link('localisation/currency/insert', $url);
		} else {
			$data['action'] = $this->url->link('localisation/currency/update', 'currency_id=' . $_GET['currency_id'] . $url);
		}

		$data['cancel'] = $this->url->link('localisation/currency', $url);

		if (isset($_GET['currency_id']) && !$this->request->isPost()) {
			$currency_info = $this->Model_Localisation_Currency->getCurrency($_GET['currency_id']);
		}

		if (isset($_POST['title'])) {
			$data['title'] = $_POST['title'];
		} elseif (isset($currency_info)) {
			$data['title'] = $currency_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($_POST['code'])) {
			$data['code'] = $_POST['code'];
		} elseif (isset($currency_info)) {
			$data['code'] = $currency_info['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($_POST['symbol_left'])) {
			$data['symbol_left'] = $_POST['symbol_left'];
		} elseif (isset($currency_info)) {
			$data['symbol_left'] = $currency_info['symbol_left'];
		} else {
			$data['symbol_left'] = '';
		}

		if (isset($_POST['symbol_right'])) {
			$data['symbol_right'] = $_POST['symbol_right'];
		} elseif (isset($currency_info)) {
			$data['symbol_right'] = $currency_info['symbol_right'];
		} else {
			$data['symbol_right'] = '';
		}

		if (isset($_POST['decimal_place'])) {
			$data['decimal_place'] = $_POST['decimal_place'];
		} elseif (isset($currency_info)) {
			$data['decimal_place'] = $currency_info['decimal_place'];
		} else {
			$data['decimal_place'] = '';
		}

		if (isset($_POST['value'])) {
			$data['value'] = $_POST['value'];
		} elseif (isset($currency_info)) {
			$data['value'] = $currency_info['value'];
		} else {
			$data['value'] = '';
		}

		if (isset($_POST['status'])) {
			$data['status'] = $_POST['status'];
		} elseif (isset($currency_info)) {
			$data['status'] = $currency_info['status'];
		} else {
			$data['status'] = '';
		}

		$this->response->setOutput($this->render('localisation/currency_form', $data));
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'localisation/currency')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify currencies!");
		}

		if ((strlen($_POST['title']) < 3) || (strlen($_POST['title']) > 32)) {
			$this->error['title'] = _l("Currency Title must be between 3 and 32 characters!");
		}

		if (strlen($_POST['code']) != 3) {
			$this->error['code'] = _l("Currency Code must contain 3 characters!");
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'localisation/currency')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify currencies!");
		}

		foreach ($_GET['selected'] as $currency_id) {
			$currency_info = $this->Model_Localisation_Currency->getCurrency($currency_id);

			if ($currency_info) {
				if ($this->config->get('config_currency') == $currency_info['code']) {
					$this->error['warning'] = _l("Warning: This currency cannot be deleted as it is currently assigned as the default store currency!");
				}

				$store_total = $this->Model_Setting_Store->getTotalStoresByCurrency($currency_info['code']);

				if ($store_total) {
					$this->error['warning'] = sprintf(_l("Warning: This currency cannot be deleted as it is currently assigned to %s stores!"), $store_total);
				}
			}

			$filter = array(
				'currencies' => array($currency_info['code']),
			);

			$order_total = $this->System_Model_Order->getTotalOrders($filter);

			if ($order_total) {
				$this->error['warning'] = sprintf(_l("Warning: This currency cannot be deleted as it is currently assigned to %s orders!"), $order_total);
			}
		}

		return $this->error ? false : true;
	}
}
