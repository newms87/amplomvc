<?php
class ControllerSaleCoupon extends Controller {
	
  	public function index() {
		$this->load->language('sale/coupon');
		
		$this->document->setTitle($this->_('heading_title'));
		
		$this->getList();
  	}
  
  	public function insert() {
		$this->load->language('sale/coupon');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_coupon->addCoupon($_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$url = $this->get_url();
									
			$this->url->redirect($this->url->link('sale/coupon', $url));
		}
	
		$this->getForm();
  	}

  	public function update() {
		$this->load->language('sale/coupon');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_coupon->editCoupon($_GET['coupon_id'], $_POST);
				
			$this->message->add('success', $this->_('text_success'));
	
			$url = $this->get_url();
						
			$this->url->redirect($this->url->link('sale/coupon', $url));
		}
	
		$this->getForm();
  	}

  	public function delete() {
		$this->load->language('sale/coupon');

		$this->document->setTitle($this->_('heading_title'));
		
		if (isset($_POST['selected']) && $this->validateDelete()) {
			foreach ($_POST['selected'] as $coupon_id) {
				$this->model_sale_coupon->deleteCoupon($coupon_id);
			}
				
			$this->message->add('success', $this->_('text_success'));
	
			$url = $this->get_url();
						
			$this->url->redirect($this->url->link('sale/coupon', $url));
		}
	
		$this->getList();
  	}

  	private function getList() {
		$this->template->load('sale/coupon_list');

  		$url_items = array('sort'=>'name','order'=>'ASC','page'=>1);
		foreach($url_items as $item=>$default){
			$$item = isset($_GET[$item])?$_GET[$item]:$default;
		}
				
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/coupon'));
							
		$this->data['insert'] = $this->url->link('sale/coupon/insert', $url);
		$this->data['delete'] = $this->url->link('sale/coupon/delete', $url);
		
		$this->data['coupons'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$coupon_total = $this->model_sale_coupon->getTotalCoupons();
	
		$results = $this->model_sale_coupon->getCoupons($data);
 
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('sale/coupon/update', 'coupon_id=' . $result['coupon_id'] . $url)
			);
						
			$this->data['coupons'][] = array(
				'coupon_id'  => $result['coupon_id'],
				'name'		=> $result['name'],
				'code'		=> $result['code'],
				'discount'	=> $result['discount'],
				'date_start' => $this->tool->format_datetime($result['date_start'], $this->language->getInfo('date_format_short')),
				'date_end'	=> $this->tool->format_datetime($result['date_end'], $this->language->getInfo('date_format_short')),
				'status'	=> ($result['status'] ? $this->_('text_enabled') : $this->_('text_disabled')),
				'selected'	=> isset($_POST['selected']) && in_array($result['coupon_id'], $_POST['selected']),
				'action'	=> $action
			);
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
		
		$sort_list = array('name','code','discount','date_start','date_end','status');
		foreach($sort_list as $s){
			$this->data['sort_'.$s] = $this->url->link('sale/coupon','sort='.$s . $url);
		}
				
		$url = $this->get_url(array('sort','order'));

		$this->pagination->init();
		$this->pagination->total = $coupon_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
		$this->template->load('sale/coupon_form');

  		$coupon_id = $this->data['coupon_id'] = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;
		
		$url = $this->get_url();
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('sale/coupon'));
		
		if (!$coupon_id) {
			$this->data['action'] = $this->url->link('sale/coupon/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/coupon/update', 'coupon_id=' . $coupon_id . $url);
		}
		
		$this->data['cancel'] = $this->url->link('sale/coupon', $url);
  		
		if ($coupon_id && ($_SERVER['REQUEST_METHOD'] != 'POST')) {
			$coupon_info = $this->model_sale_coupon->getCoupon($coupon_id);
		}
		
		$defaults = array(
			'name'=>'',
			'code'=>'',
			'type'=>'',
			'discount'=>'',
			'logged'=>'',
			'shipping'=>'',
			'shipping_geozone'=>0,
			'total'=>'',
			'date_start'=>date('Y-m-d', time()),
			'date_end'=>date('Y-m-d', time()),
			'uses_total'=>'',
			'uses_customer'=>'',
			'coupon_products'=>array(),
			'coupon_categories'=>array(),
			'coupon_customers'=>array(),
			'status'=>1
		);

		foreach($defaults as $d=>$default){
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($coupon_info[$d])) {
				$this->data[$d] = $coupon_info[$d];
			} elseif(!$coupon_id) {
				$this->data[$d] = $default;
			}
		}
		
		if(!isset($this->data['coupon_products'])) {
			$products = $this->model_sale_coupon->getCouponProducts($coupon_id);
			
			$this->data['coupon_products'] = array();
			foreach ($products as $product) {
				$this->data['coupon_products'][] = $this->model_catalog_product->getProduct($product['product_id']);
			}
		}
		
		if(!isset($this->data['coupon_categories'])) {
			$categories = $this->model_sale_coupon->getCouponCategories($coupon_id);
			
			$this->data['coupon_categories'] = array();
			foreach ($categories as $category_id) {
				$this->data['coupon_categories'][] = $this->model_catalog_category->getCategory($category_id);
			}
		}
		
		if(!isset($this->data['coupon_customers'])) {
			$customers = $this->model_sale_coupon->getCouponCustomers($coupon_id);
			
			$this->data['coupon_customers'] = array();
			foreach ($customers as $customer) {
				$this->data['coupon_customers'][] = $this->model_sale_customer->getCustomer($customer['customer_id']);
			}
		}
				
		if(!isset($this->data['date_start'])){
			$this->data['date_start'] = date('Y-m-d', strtotime($coupon_info['date_start']));
		}
		
		if(!isset($this->data['date_end'])){
			$this->data['date_end'] = date('Y-m-d', strtotime($coupon_info['date_end']));
		}
		
		$this->data['data_geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
	
  	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/coupon')) {
				$this->error['warning'] = $this->_('error_permission');
		}
			
		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 128)) {
			$this->error['name'] = $this->_('error_name');
			}
			
		if ((strlen($_POST['code']) < 3) || (strlen($_POST['code']) > 10)) {
				$this->error['code'] = $this->_('error_code');
		}
		
		$coupon_info = $this->model_sale_coupon->getCouponByCode($_POST['code']);
		
		if ($coupon_info) {
			if (!isset($_GET['coupon_id'])) {
				$this->error['warning'] = $this->_('error_exists');
			} elseif ($coupon_info['coupon_id'] != $_GET['coupon_id'])  {
				$this->error['warning'] = $this->_('error_exists');
			}
		}
	
		return $this->error ? false : true;
  	}

  	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/coupon')) {
				$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
  	}
	
	public function history() {
		$this->template->load('sale/coupon_history');
		$coupon_id = $this->data['coupon_id'] = isset($_GET['coupon_id'])?$_GET['coupon_id']:0;
		
		$this->language->load('sale/coupon');
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		
		$this->data['histories'] = array();
			
		$results = $this->model_sale_coupon->getCouponHistories($coupon_id, ($page - 1) * 10, 10);
				
		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'	=> $result['order_id'],
				'customer'	=> $result['customer'],
				'amount'	=> $result['amount'],
				'date_added' => $this->tool->format_datetime($result['date_added'], $this->language->getInfo('date_format_short')),
			);
		}
		
		$history_total = $this->model_sale_coupon->getTotalCouponHistories($coupon_id);
			
		$this->pagination->init();
		$this->pagination->total = $history_total;
		$this->data['pagination'] = $this->pagination->render();
		
		
		$this->response->setOutput($this->render());
  	}
	
	private function get_url($override=array()){
		$url = '';
		$filters = !empty($override)?$override:array('sort', 'order', 'page');
		foreach($filters as $f)
			if (isset($_GET[$f]))
				$url .= "&$f=" . $_GET[$f];
		return $url;
	}
}
