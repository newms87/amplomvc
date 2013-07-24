<?php
class Admin_Controller_Localisation_Currency extends Controller
{
	

	public function index()
	{
		$this->load->language('localisation/currency');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
	}

	public function insert()
	{
		$this->load->language('localisation/currency');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Currency->addCurrency($_POST);
			
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
						
			$this->url->redirect($this->url->link('localisation/currency', $url));
		}

		$this->getForm();
	}

	public function update()
	{
		$this->load->language('localisation/currency');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Localisation_Currency->editCurrency($_GET['currency_id'], $_POST);
			
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
						
			$this->url->redirect($this->url->link('localisation/currency', $url));
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->load->language('localisation/currency');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $currency_id) {
				$this->Model_Localisation_Currency->deleteCurrency($currency_id);
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

			$this->url->redirect($this->url->link('localisation/currency', $url));
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('localisation/currency_list');

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/currency', $url));

		$this->data['insert'] = $this->url->link('localisation/currency/insert', $url);
		$this->data['delete'] = $this->url->link('localisation/currency/delete', $url);
		
		$this->data['currencies'] = array();

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
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('localisation/currency/update', 'currency_id=' . $result['currency_id'] . $url)
			);
						
			$this->data['currencies'][] = array(
				'currency_id'	=> $result['currency_id'],
				'title'			=> $result['title'] . (($result['code'] == $this->config->get('config_currency')) ? $this->_('text_default') : null),
				'code'			=> $result['code'],
				'value'			=> $result['value'],
				'date_modified' => $this->date->format($result['date_modified'], $this->language->getInfo('date_format_short')),
				'selected'		=> isset($_POST['selected']) && in_array($result['currency_id'], $_POST['selected']),
				'action'		=> $action
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
		
		$this->data['sort_title'] = $this->url->link('localisation/currency', 'sort=title' . $url);
		$this->data['sort_code'] = $this->url->link('localisation/currency', 'sort=code' . $url);
		$this->data['sort_value'] = $this->url->link('localisation/currency', 'sort=value' . $url);
		$this->data['sort_date_modified'] = $this->url->link('localisation/currency', 'sort=date_modified' . $url);
		
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $currency_total;
		$this->data['pagination'] = $this->pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('localisation/currency_form');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = '';
		}
		
 		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('localisation/currency', $url));

		if (!isset($_GET['currency_id'])) {
			$this->data['action'] = $this->url->link('localisation/currency/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('localisation/currency/update', 'currency_id=' . $_GET['currency_id'] . $url);
		}
				
		$this->data['cancel'] = $this->url->link('localisation/currency', $url);

		if (isset($_GET['currency_id']) && !$this->request->isPost()) {
			$currency_info = $this->Model_Localisation_Currency->getCurrency($_GET['currency_id']);
		}

		if (isset($_POST['title'])) {
			$this->data['title'] = $_POST['title'];
		} elseif (isset($currency_info)) {
			$this->data['title'] = $currency_info['title'];
		} else {
			$this->data['title'] = '';
		}

		if (isset($_POST['code'])) {
			$this->data['code'] = $_POST['code'];
		} elseif (isset($currency_info)) {
			$this->data['code'] = $currency_info['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($_POST['symbol_left'])) {
			$this->data['symbol_left'] = $_POST['symbol_left'];
		} elseif (isset($currency_info)) {
			$this->data['symbol_left'] = $currency_info['symbol_left'];
		} else {
			$this->data['symbol_left'] = '';
		}

		if (isset($_POST['symbol_right'])) {
			$this->data['symbol_right'] = $_POST['symbol_right'];
		} elseif (isset($currency_info)) {
			$this->data['symbol_right'] = $currency_info['symbol_right'];
		} else {
			$this->data['symbol_right'] = '';
		}

		if (isset($_POST['decimal_place'])) {
			$this->data['decimal_place'] = $_POST['decimal_place'];
		} elseif (isset($currency_info)) {
			$this->data['decimal_place'] = $currency_info['decimal_place'];
		} else {
			$this->data['decimal_place'] = '';
		}

		if (isset($_POST['value'])) {
			$this->data['value'] = $_POST['value'];
		} elseif (isset($currency_info)) {
			$this->data['value'] = $currency_info['value'];
		} else {
			$this->data['value'] = '';
		}

		if (isset($_POST['status'])) {
				$this->data['status'] = $_POST['status'];
		} elseif (isset($currency_info)) {
			$this->data['status'] = $currency_info['status'];
		} else {
				$this->data['status'] = '';
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validateForm()
	{
		if (!$this->user->hasPermission('modify', 'localisation/currency')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if ((strlen($_POST['title']) < 3) || (strlen($_POST['title']) > 32)) {
			$this->error['title'] = $this->_('error_title');
		}

		if (strlen($_POST['code']) != 3) {
			$this->error['code'] = $this->_('error_code');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete()
	{
		if (!$this->user->hasPermission('modify', 'localisation/currency')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		foreach ($_POST['selected'] as $currency_id) {
			$currency_info = $this->Model_Localisation_Currency->getCurrency($currency_id);

			if ($currency_info) {
				if ($this->config->get('config_currency') == $currency_info['code']) {
					$this->error['warning'] = $this->_('error_default');
				}
				
				$store_total = $this->Model_Setting_Store->getTotalStoresByCurrency($currency_info['code']);
	
				if ($store_total) {
					$this->error['warning'] = sprintf($this->_('error_store'), $store_total);
				}
			}
			
			$order_total = $this->Model_Sale_Order->getTotalOrdersByCurrencyId($currency_id);

			if ($order_total) {
				$this->error['warning'] = sprintf($this->_('error_order'), $order_total);
			}
		}
		
		return $this->error ? false : true;
	}
}