<?php
class Admin_Controller_Sale_Voucher extends Controller 
{
	
	
  	public function index()
  	{
		$this->load->language('sale/voucher');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert()
  	{
		$this->load->language('sale/voucher');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Sale_Voucher->addVoucher($_POST);
			
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
			
			$this->url->redirect($this->url->link('sale/voucher', $url));
		}
	
		$this->getForm();
  	}

  	public function update()
  	{
		$this->load->language('sale/voucher');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validateForm()) {
			$this->Model_Sale_Voucher->editVoucher($_GET['voucher_id'], $_POST);
				
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
			
			$this->url->redirect($this->url->link('sale/voucher', $url));
		}
	
		$this->getForm();
  	}

  	public function delete()
  	{
		$this->load->language('sale/voucher');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $voucher_id) {
				$this->Model_Sale_Voucher->deleteVoucher($voucher_id);
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
			
			$this->url->redirect($this->url->link('sale/voucher', $url));
		}
	
		$this->getList();
  	}

  	private function getList()
  	{
		$this->template->load('sale/voucher_list');

		if (isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		} else {
			$sort = 'v.date_added';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher', $url));

		$this->data['insert'] = $this->url->link('sale/voucher/insert', $url);
		$this->data['delete'] = $this->url->link('sale/voucher/delete', $url);
		
		$this->data['vouchers'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$voucher_total = $this->Model_Sale_Voucher->getTotalVouchers();
	
		$results = $this->Model_Sale_Voucher->getVouchers($data);
 
		foreach ($results as $result) {
			$action = array();
									
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/voucher/update', 'voucher_id=' . $result['voucher_id'] . $url)
			);
						
			$this->data['vouchers'][] = array(
				'voucher_id' => $result['voucher_id'],
				'code'		=> $result['code'],
				'from'		=> $result['from_name'],
				'to'			=> $result['to_name'],
				'theme'		=> $result['theme'],
				'amount'	=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'status'	=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'selected'	=> isset($_POST['selected']) && in_array($result['voucher_id'], $_POST['selected']),
				'action'	=> $action
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
		
		$this->data['sort_code'] = $this->url->link('sale/voucher', 'sort=v.code' . $url);
		$this->data['sort_from'] = $this->url->link('sale/voucher', 'sort=v.from_name' . $url);
		$this->data['sort_to'] = $this->url->link('sale/voucher', 'sort=v.to_name' . $url);
		$this->data['sort_theme'] = $this->url->link('sale/voucher', 'sort=theme' . $url);
		$this->data['sort_amount'] = $this->url->link('sale/voucher', 'sort=v.amount' . $url);
		$this->data['sort_status'] = $this->url->link('sale/voucher', 'sort=v.date_end' . $url);
		$this->data['sort_date_added'] = $this->url->link('sale/voucher', 'sort=v.date_added' . $url);
				
		$url = '';

		if (isset($_GET['sort'])) {
			$url .= '&sort=' . $_GET['sort'];
		}
												
		if (isset($_GET['order'])) {
			$url .= '&order=' . $_GET['order'];
		}

		$this->pagination->init();
		$this->pagination->total = $voucher_total;
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
		$this->template->load('sale/voucher_form');

		if (isset($_GET['voucher_id'])) {
			$this->data['voucher_id'] = $_GET['voucher_id'];
		} else {
			$this->data['voucher_id'] = 0;
		}
				
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}
		
		if (isset($this->error['from_name'])) {
			$this->data['error_from_name'] = $this->error['from_name'];
		} else {
			$this->data['error_from_name'] = '';
		}
		
		if (isset($this->error['from_email'])) {
			$this->data['error_from_email'] = $this->error['from_email'];
		} else {
			$this->data['error_from_email'] = '';
		}
		
		if (isset($this->error['to_name'])) {
			$this->data['error_to_name'] = $this->error['to_name'];
		} else {
			$this->data['error_to_name'] = '';
		}
		
		if (isset($this->error['to_email'])) {
			$this->data['error_to_email'] = $this->error['to_email'];
		} else {
			$this->data['error_to_email'] = '';
		}
		
		if (isset($this->error['amount'])) {
			$this->data['error_amount'] = $this->error['amount'];
		} else {
			$this->data['error_amount'] = '';
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/voucher', $url));

		if (!isset($_GET['voucher_id'])) {
			$this->data['action'] = $this->url->link('sale/voucher/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/voucher/update', 'voucher_id=' . $_GET['voucher_id'] . $url);
		}
		
		$this->data['cancel'] = $this->url->link('sale/voucher', $url);
  		
		if (isset($_GET['voucher_id']) && (!!$this->request->isPost())) {
				$voucher_info = $this->Model_Sale_Voucher->getVoucher($_GET['voucher_id']);
		}

		if (isset($_POST['code'])) {
				$this->data['code'] = $_POST['code'];
		} elseif (!empty($voucher_info)) {
			$this->data['code'] = $voucher_info['code'];
		} else {
				$this->data['code'] = '';
		}
		
		if (isset($_POST['from_name'])) {
				$this->data['from_name'] = $_POST['from_name'];
		} elseif (!empty($voucher_info)) {
			$this->data['from_name'] = $voucher_info['from_name'];
		} else {
				$this->data['from_name'] = '';
		}
		
		if (isset($_POST['from_email'])) {
				$this->data['from_email'] = $_POST['from_email'];
		} elseif (!empty($voucher_info)) {
			$this->data['from_email'] = $voucher_info['from_email'];
		} else {
				$this->data['from_email'] = '';
		}

		if (isset($_POST['to_name'])) {
				$this->data['to_name'] = $_POST['to_name'];
		} elseif (!empty($voucher_info)) {
			$this->data['to_name'] = $voucher_info['to_name'];
		} else {
				$this->data['to_name'] = '';
		}
		
		if (isset($_POST['to_email'])) {
				$this->data['to_email'] = $_POST['to_email'];
		} elseif (!empty($voucher_info)) {
			$this->data['to_email'] = $voucher_info['to_email'];
		} else {
				$this->data['to_email'] = '';
		}
 
 		$this->data['voucher_themes'] = $this->Model_Sale_VoucherTheme->getVoucherThemes();

		if (isset($_POST['voucher_theme_id'])) {
				$this->data['voucher_theme_id'] = $_POST['voucher_theme_id'];
		} elseif (!empty($voucher_info)) {
			$this->data['voucher_theme_id'] = $voucher_info['voucher_theme_id'];
		} else {
				$this->data['voucher_theme_id'] = '';
		}
		
		if (isset($_POST['message'])) {
				$this->data['message'] = $_POST['message'];
		} elseif (!empty($voucher_info)) {
			$this->data['message'] = $voucher_info['message'];
		} else {
				$this->data['message'] = '';
		}
		
		if (isset($_POST['amount'])) {
				$this->data['amount'] = $_POST['amount'];
		} elseif (!empty($voucher_info)) {
			$this->data['amount'] = $voucher_info['amount'];
		} else {
				$this->data['amount'] = '';
		}
	
		if (isset($_POST['status'])) {
				$this->data['status'] = $_POST['status'];
		} elseif (!empty($voucher_info)) {
			$this->data['status'] = $voucher_info['status'];
		} else {
				$this->data['status'] = 1;
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm()
  	{
		if (!$this->user->hasPermission('modify', 'sale/voucher')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		if ((strlen($_POST['code']) < 3) || (strlen($_POST['code']) > 10)) {
				$this->error['code'] = $this->_('error_code');
		}
		
		$voucher_info = $this->Model_Sale_Voucher->getVoucherByCode($_POST['code']);
		
		if ($voucher_info) {
			if (!isset($_GET['voucher_id'])) {
				$this->error['warning'] = $this->_('error_exists');
			} elseif ($voucher_info['voucher_id'] != $_GET['voucher_id']) {
				$this->error['warning'] = $this->_('error_exists');
			}
		}
							
		if ((strlen($_POST['to_name']) < 1) || (strlen($_POST['to_name']) > 64)) {
				$this->error['to_name'] = $this->_('error_to_name');
		}
		
		if ((strlen($_POST['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['to_email'])) {
				$this->error['to_email'] = $this->_('error_email');
		}
		
		if ((strlen($_POST['from_name']) < 1) || (strlen($_POST['from_name']) > 64)) {
				$this->error['from_name'] = $this->_('error_from_name');
		}
		
		if ((strlen($_POST['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['from_email'])) {
				$this->error['from_email'] = $this->_('error_email');
		}
		
		if ($_POST['amount'] < 1) {
				$this->error['amount'] = $this->_('error_amount');
		}

		return $this->error ? false : true;
  	}

  	private function validateDelete()
  	{
		if (!$this->user->hasPermission('modify', 'sale/voucher')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		foreach ($_POST['selected'] as $voucher_id) {
			$order_voucher_info = $this->Model_Sale_Order->getOrderVoucherByVoucherId($voucher_id);
			
			if ($order_voucher_info) {
				$this->error['warning'] = sprintf($this->_('error_order'), $this->url->link('sale/order/info', 'order_id=' . $order_voucher_info['order_id']));
				
				break;
			}
		}
		
		return $this->error ? false : true;
  	}
	
	public function history()
	{
		$this->template->load('sale/voucher_history');
		$this->language->load('sale/voucher');
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['histories'] = array();
			
		$results = $this->Model_Sale_Voucher->getVoucherHistories($_GET['voucher_id'], ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'	=> $result['order_id'],
				'customer'	=> $result['customer'],
				'amount'	=> $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
			);
			}
		
		$history_total = $this->Model_Sale_Voucher->getTotalVoucherHistories($_GET['voucher_id']);
			
		$this->pagination->init();
		$this->pagination->total = $history_total;
		$this->data['pagination'] = $this->pagination->render();
		
		
		$this->response->setOutput($this->render());
  	}
	
	public function send()
	{
		$this->language->load('sale/voucher');
		
		$json = array();
		
		if (!$this->user->hasPermission('modify', 'sale/voucher')) {
				$json['error'] = $this->_('error_permission');
		} elseif (isset($_GET['voucher_id'])) {
			$this->Model_Sale_Voucher->sendVoucher($_GET['voucher_id']);
			
			$json['success'] = $this->_('text_sent');
		}
		
		$this->response->setOutput(json_encode($json));
  	}
}