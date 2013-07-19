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
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return'));
		
		$sort_filter = $this->sort->getQueryDefaults('date_added', 'ASC');
		
		$return_total = $this->Model_Account_Return->getTotalReturns($sort_filter);
		$returns = $this->Model_Account_Return->getReturns($sort_filter);
		
		foreach ($returns as &$return) {
			$return['name'] = $return['firstname'] . ' ' . $return['lastname'];
			$return['date_added'] = $this->date->format($return['date_added'], 'short');
			$return['href'] = $this->url->link('account/return/info', 'return_id=' . $return['return_id']);
		}
		
		$this->data['returns'] = $returns;

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
		$this->language->load('account/return');
		
		if (!$this->customer->isLogged()) {
			$query = array(
				'redirect' => $this->url->link('account/return/info', 'return_id=' . $return_id)
			);
			
			$this->url->redirect($this->url->link('account/login', $query));
		}
		
		$return_id = isset($_GET['return_id']) ? $_GET['return_id'] : 0;
		
		$this->document->setTitle($this->_('text_return'));
		
		$url_query = $this->url->getQuery('page');
		
		//Breadcrumbs	
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return', $url_query));
		$this->breadcrumb->add($this->_('text_return'), $this->url->link('account/return/info', 'return_id=' . $return_id . '&' . $url_query));
		
		$return_info = $this->Model_Account_Return->getReturn($return_id);
		
		if ($return_info) {
			$this->template->load('account/return_info');
			$this->language->set('heading_title', $this->_('text_return'));
			
			$return_info['comment'] = nl2br($return_info['comment']);
			$return_info['opened'] = $return_info['opened'] ? $this->_('text_yes') : $this->_('text_no');
			$return_info['return_status'] = $this->order->getReturnStatus($return_info['return_status_id']);
			
			$this->data = $return_info;
			
			$this->data['date_ordered'] = $this->date->format($return_info['date_ordered'], 'date_format_short');
			$this->data['date_added'] = $this->date->format($return_info['date_added'], 'date_format_short');
			
			$histories = $this->Model_Account_Return->getReturnHistories($return_id);
			
			foreach ($histories as &$history) {
				$history['return_status'] = $this->order->getReturnStatus($history['return_status_id']);
				$history['date_added'] = $this->date->format($history['date_added'], 'date_format_short');
				$history['comment'] = nl2br($history['comment']);
			} unset($history);
			
			$this->data['histories'] = $histories;
		
			$this->data['continue'] = $this->url->link('account/return', $url_query);

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
			
			$this->language->set('heading_title', $this->_('text_return'));
			
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
		$this->language->load('account/return');

		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
		
		$order_lookup = isset($_GET['order_lookup']) ? $_GET['order_lookup'] : 0;
		
		if ($this->request->isPost() && $this->validate()) {
			$return_ids = array();
			
			foreach ($_POST['return_products'] as $product) {
				$return_data = $_POST + $product;
				
				$return_data['rma'] = $this->Model_Account_Return->generateRma($return_data);
				$return_data['quantity'] = $return_data['return_quantity'];
				
				$return_ids[] = $this->Model_Account_Return->addReturn($return_data);
				
				//Send Customer Confirmation Email
				$this->mail->init();
				
				$this->mail->setTo($return_data['email']);
				$this->mail->setCc($this->config->get('config_email'));
				$this->mail->setFrom($this->config->get('config_email'));
				$this->mail->setSender($this->config->get('config_name'));
				$this->mail->setSubject(html_entity_decode($this->_('text_mail_subject'), ENT_QUOTES, 'UTF-8'));
				$this->mail->setHtml(html_entity_decode($this->_('text_mail_message'), ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
				
				//Send Admin Notification Email
				$this->mail->init();
				
				$this->mail->setTo($this->config->get('config_email'));
				$this->mail->setFrom($this->config->get('config_email'));
				$this->mail->setSender($this->config->get('config_name'));
				$this->mail->setSubject(html_entity_decode($this->_('text_mail_admin_subject'), ENT_QUOTES, 'UTF-8'));
				$this->mail->setHtml(html_entity_decode($this->_('text_mail_admin_message'), ENT_QUOTES, 'UTF-8'));
				$this->mail->send();
			}
			
			$this->url->redirect($this->url->link('account/return/success', array('return_ids' => $return_ids)));
		}
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_account'), $this->url->link('account/account'));
		$this->breadcrumb->add($this->_('text_return_list'), $this->url->link('account/return'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return/insert'));
		
		$this->data['action'] = $this->url->link('account/return/insert');
		
		$customer_orders = $this->customer->getOrders();
		
		if ($order_lookup) {
			//If order does not belong to this customer, lookup the order info
			if (!empty($customer_orders) && !in_array($order_id, array_column($customer_orders, 'order_id'))) {
				$order_info = $this->order->get($order_id, false);
				
				//If the lookup email does not match the order email, customer may not view this order
				if (empty($_GET['email']) || $_GET['email'] !== $order_info['email']) {
					$this->message->add('warning', $this->_('error_invalid_order_id', $order_id));
					$this->url->redirect($this->url->link('account/return/insert'));
				}
			}
			//This order belongs to this customer, so they may request an exchange
			else {
				$order_lookup = false;
			}
		}
		
		if (empty($order_info)) {
			if ($order_id) {
				foreach ($customer_orders as $order) {
					if ((int)$order['order_id'] === (int)$order_id) {
						$order_info = $order;
						break;
					}
				} 
			} else {
				$order_info = reset($customer_orders);
			}
		}
		
		if ($order_info) {
			$order_info['date_ordered'] = $this->date->format($order_info['date_added']);
			
			$order_products = $this->System_Model_Order->getOrderProducts($order_info['order_id']);
			
			foreach ($order_products as $key => &$product) {
				$product_info = $this->Model_Catalog_Product->getProductInfo($product['product_id']);
				
				if ($product_info) {
					$product['name'] = $product_info['name'];
					$product['model'] = $product_info['model'];
					$product['price'] = $this->currency->format($product['price']);
					
					$return_policy = $this->cart->getReturnPolicy($product_info['return_policy_id']);
					
					if ($return_policy['days'] < 0) {
						$product['no_return'] = $this->_('text_is_final');
					} else {
						$return_date = $this->date->add($order_info['date_added'], $return_policy['days'] . ' days');
						
						if ($this->date->isInPast($return_date)) {
							$product['no_return'] = $this->_('text_past_return_date', $this->date->format($return_date, 'short'));
						}
					}
					
					$product['return_policy'] = $return_policy;
					
					$product_defaults = array(
						'return_quantity' => 0,
						'return_reason_id' => '',
						'comment' => '',
						'opened' => 0,
					);
					
					foreach ($product_defaults as $key => $default) {
						if (isset($_POST['return_products'][$product['product_id']][$key])) {
							$product[$key] = $_POST['return_products'][$product['product_id']][$key];
						} else {
							$product[$key] = $default;
						}
					}
					
				} else {
					unset($order_products[$key]);
				}
			} unset($product);
			
			$order_info['return_products'] = $order_products;
		}
		
		$defaults = array(
			'order_id' => $order_id,
			'date_ordered' => '',
			'firstname' => $this->customer->info('firstname'),
			'lastname' => $this->customer->info('lastname'),
			'email' => $this->customer->info('email'),
			'telephone' => $this->customer->info('telephone'),
			'captcha' => '',
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($order_info[$key])) {
				$this->data[$key] = $order_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['return_products'] = $order_info['return_products'];
		
		if (!empty($customer_orders)) {
			foreach ($customer_orders as &$order) {
				$product_count = $this->System_Model_Order->getTotalOrderProducts($order['order_id']);
				
				$order['display'] = $this->_('text_order_display', $order['order_id'], $product_count);
			} unset($order);
		}
		
		$this->data['customer_orders'] = $customer_orders;
		
		$this->data['date_ordered_display'] = $this->date->format($this->data['date_ordered'], $this->language->getInfo('date_format_short'));
		$this->data['data_return_reasons'] = $this->order->getReturnReasons();
		
		$this->data['back'] = $this->url->link('account/account');
		$this->data['return_product_url'] = $this->url->link('account/return/insert');
		
		$this->data['order_lookup'] = $order_lookup;
		$this->data['order_lookup_action'] = $this->url->link('account/return/find');
		
		if (!$this->customer->isLogged()) {
			$this->message->add('warning', $this->_('error_customer_logged'));
		}
		
		
		//Ajax Urls
		$this->data['url_captcha_image'] = $this->url->ajax('account/return/captcha');
		
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

	public function find()
	{
		$this->language->load('account/return');
		
		$url_query = '';
		
		if ($this->request->isPost() && !empty($_POST['ol_order_id']) && !empty($_POST['ol_email'])) {
			$order = $this->order->get($_POST['ol_order_id']);
			
			if (!empty($order)) {
				if ($order['email'] === $_POST['ol_email']) {
					$query = array(
						'order_id' => $order['order_id'],
						'email' => $order['email'],
						'order_lookup' => 1,
					);
					
					$url_query = http_build_query($query);
					
					
					$this->message->add('notify', $this->_('notify_order_lookup_guest', $order['email']));
				} else {
					$this->message->add('warning', $this->_('error_order_lookup_email'));
				}
			} else {
				$this->message->add("warning", $this->_('error_order_lookup'));
			}
		}
		else {
			$this->message->add("warning", $this->_('error_order_lookup'));
		}
		
		$this->url->redirect($this->url->link('account/return/insert', $url_query));
	}
	
  	public function success()
  	{
		$this->template->load('account/return_success');
		$this->language->load('account/return');

		$this->document->setTitle($this->_('return_success_title'));
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_return_list'), $this->url->link('account/return'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('account/return/insert'));
		$this->breadcrumb->add($this->_('return_success_title'), $this->url->link('account/return/success'));
		
		$returns = array();
		
		if (!empty($_GET['return_ids'])) {
			foreach ($_GET['return_ids'] as $return_id) {
				$returns[] = $this->Model_Account_Return->getReturn($return_id);
			}
		}
		
		$this->data['returns'] = $returns;
		
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
		if (empty($_POST['order_id'])) {
			$this->error['order_id'] = $this->_('error_order_id');
		}
		
		if (!$this->validation->text($_POST['firstname'], 1, 64)) {
			$this->error['firstname'] = $this->_('error_firstname');
		}

		if (!$this->validation->text($_POST['lastname'], 1, 64)) {
			$this->error['lastname'] = $this->_('error_lastname');
		}

		if (!$this->validation->email($_POST['email'])) {
			$this->error['email'] = $this->_('error_email');
		}
		
		if (!$this->validation->phone($_POST['telephone'])) {
			$this->error['telephone'] = $this->_('error_telephone');
		}

		$has_product = false;
		
		if (!empty($_POST['return_products'])) {
			foreach ($_POST['return_products'] as $key => $product) {
				if (!empty($product['return_quantity'])) {
					$has_product = true;
					
					if (empty($product['return_reason_id']) && $product['return_reason_id'] !== '0') {
						$this->error["return_products[$product[product_id]][return_reason_id"] = $this->_('error_reason');
					}
				}
			}
		}
		
		if (!$has_product) {
			$this->error['return_products'] = $this->_('error_return_products');
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
}
