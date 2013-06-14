<?php
class Catalog_Controller_Account_Return extends Controller 
{
	
	public function index()
	{
		$this->template->load('account/return_list');

		if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('account/return');

			$this->url->redirect($this->url->link('account/login'));
		}
 
		$this->language->load('account/return');

		$this->document->setTitle($this->_('heading_title'));
		
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return', $url));
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['returns'] = array();
		
		$return_total = $this->Model_Account_Return->getTotalReturns();
		
		$results = $this->Model_Account_Return->getReturns(($page - 1) * 10, 10);
		
		foreach ($results as $result) {
			$this->data['returns'][] = array(
				'return_id'  => $result['return_id'],
				'order_id'	=> $result['order_id'],
				'name'		=> $result['firstname'] . ' ' . $result['lastname'],
				'status'	=> $result['status'],
				'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
				'href'		=> $this->url->link('account/return/info', 'return_id=' . $result['return_id'] . $url)
			);
		}

		$this->pagination->init();
		$this->pagination->total = $return_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
						
		$this->response->setOutput($this->render());
	}
	
	public function info()
	{
		$this->load->language('account/return');
		
		if (isset($_GET['return_id'])) {
			$return_id = $_GET['return_id'];
		} else {
			$return_id = 0;
		}
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id);
			
			$this->url->redirect($this->url->link('account/login'));
		}
		
		$return_info = $this->Model_Account_Return->getReturn($return_id);
		
		if ($return_info) {
		$this->template->load('account/return_info');

			$this->document->setTitle($this->_('text_return'));
			
			$this->language->set('heading_title', $this->_('text_return'));
			
			$url = $this->get_url();
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return', $url));
			$this->breadcrumb->add($this->_('text_return'), $this->url->link('account/return/info', 'return_id=' . $_GET['return_id'] . $url));
			
			$this->data['date_ordered'] = date($this->language->getInfo('date_format_short'), strtotime($return_info['date_ordered']));
			$this->data['date_added'] = date($this->language->getInfo('date_format_short'), strtotime($return_info['date_added']));
			
			
			$this->data['return_id'] = $return_info['return_id'];
			$this->data['order_id'] = $return_info['order_id'];
			$this->data['firstname'] = $return_info['firstname'];
			$this->data['lastname'] = $return_info['lastname'];
			$this->data['email'] = $return_info['email'];
			$this->data['telephone'] = $return_info['telephone'];
			$this->data['product'] = $return_info['product'];
			$this->data['model'] = $return_info['model'];
			$this->data['quantity'] = $return_info['quantity'];
			$this->data['reason'] = $return_info['reason'];
			$this->data['opened'] = $return_info['opened'] ? $this->_('text_yes') : $this->_('text_no');
			$this->data['comment'] = nl2br($return_info['comment']);
			$this->data['action'] = $return_info['action'];
						
			$this->data['histories'] = array();
			
			$results = $this->Model_Account_Return->getReturnHistories($_GET['return_id']);
			
				foreach ($results as $result) {
				$this->data['histories'][] = array(
						'date_added' => $this->date->format($result['date_added'], $this->language->getInfo('date_format_short')),
						'status'	=> $result['status'],
						'comment'	=> nl2br($result['comment'])
				);
				}
			
			$this->data['continue'] = $this->url->link('account/return', $url);

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
									
			$this->response->setOutput($this->render());
		} else {
		$this->template->load('error/not_found');

			$this->document->setTitle($this->_('text_return'));
			
			$this->language->set('heading_title', $this->_('text_return'));
			
			$url = $this->get_url();
			
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return'));
			$this->breadcrumb->add($this->_('text_return'), $this->url->link('account/return/info', 'return_id=' . $return_id . $url));
			
			$this->data['continue'] = $this->url->link('account/return');

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);
						
			$this->response->setOutput($this->render());
		}
	}
		
	public function insert()
	{
		$this->template->load('account/return_form');

		$order_id = isset($_GET['order_id'])?$_GET['order_id']:0;
		$this->language->load('account/return');

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->Model_Account_Return->addReturn($_POST);
			
			$this->url->redirect($this->url->link('account/return/success'));
		}
							
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return/insert'));
		
		$this->data['action'] = $this->url->link('account/return/insert');
	
		if ($order_id) {
			$order_info = $this->Model_Account_Order->getOrder($order_id);
		}
		
		if (isset($_GET['product_id'])) {
			$product_info = $this->Model_Catalog_Product->getProduct($_GET['product_id']);
		}
		
		$defaults = array('order_id'=>'',
								'date_ordered'=>'',
								'firstname'=>$this->customer->info('firstname'),
								'lastname'=>$this->customer->info('lastname'),
								'email'=>$this->customer->info('email'),
								'telephone'=>$this->customer->info('telephone'),
								'product'=>'',
								'model'=>'',
								'quantity'=>1,
								'opened'=>false,
								'return_reason_id'=>'',
								'comment'=>'',
								'captcha'=>''
							);
		
		$force_default = array('return_reason_id','comment','captcha','quantity','opened');
		
		foreach ($defaults as $d=>$default) {
			if (isset($_POST[$d]))
				$this->data[$d] = $_POST[$d];
			elseif (isset($order_info[$d]))
				$this->data[$d] = $order_info[$d];
			elseif(!$order_id || in_array($d,$force_default))
				$this->data[$d] = $default;
		}
		
		if (!isset($this->data['date_ordered'])) {
			$this->data['date_ordered'] = isset($order_info['date_added'])?date('Y-m-d', strtotime($order_info['date_added'])):$defaults['date_ordered'];
		}
				
		if (!isset($this->data['product'])) {
			$this->data['product'] = isset($product_info['name'])?$product_info['name']:$defaults['product'];
		}
		
		if (!isset($this->data['model'])) {
			$this->data['model'] = isset($product_info['model'])?$product_info['model']:$defaults['model'];
		}
														
		$this->data['return_reasons'] = $this->Model_Localisation_ReturnReason->getReturnReasons();
		
		$this->data['back'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
				
		$this->response->setOutput($this->render());
  	}
	
  	public function success()
  	{
		$this->template->load('common/success');

		$this->language->load('account/return');

		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return'));
		
		$this->data['continue'] = $this->url->link('common/home');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
				
 		$this->response->setOutput($this->render());
	}
		
  	private function validate()
  	{
		if (!$_POST['order_id']) {
				$this->error['order_id'] = $this->_('error_order_id');
		}
		
		if ((strlen($_POST['firstname']) < 1) || (strlen($_POST['firstname']) > 32)) {
				$this->error['firstname'] = $this->_('error_firstname');
		}

		if ((strlen($_POST['lastname']) < 1) || (strlen($_POST['lastname']) > 32)) {
				$this->error['lastname'] = $this->_('error_lastname');
		}

		if ((strlen($_POST['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $_POST['email'])) {
				$this->error['email'] = $this->_('error_email');
		}
		
		if ((strlen($_POST['telephone']) < 3) || (strlen($_POST['telephone']) > 32)) {
				$this->error['telephone'] = $this->_('error_telephone');
		}
		
		if ((strlen($_POST['product']) < 1) || (strlen($_POST['product']) > 255)) {
			$this->error['product'] = $this->_('error_product');
		}
		
		if ((strlen($_POST['model']) < 1) || (strlen($_POST['model']) > 64)) {
			$this->error['model'] = $this->_('error_model');
		}

		if (empty($_POST['return_reason_id'])) {
			$this->error['reason'] = $this->_('error_reason');
		}
				
		if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $_POST['captcha'])) {
				$this->error['captcha'] = $this->_('error_captcha');
		}

		return $this->error ? false : true;
  	}
	
	public function captcha()
	{
		$this->session->data['captcha'] = $this->captcha->getCode();
		
		$this->captcha->showImage();
	}
	
	private function get_url($filters=null)
	{
		$url = '';
		$filters = $filters?$filters:array('page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}
