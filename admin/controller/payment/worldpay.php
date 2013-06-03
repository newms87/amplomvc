<?php
class Admin_Controller_Payment_Worldpay extends Controller 
{
	

	public function index()
	{
		$this->template->load('payment/worldpay');

		$this->load->language('payment/worldpay');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('worldpay', $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/worldpay'));

		$this->data['action'] = $this->url->link('payment/worldpay');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['worldpay_merchant'])) {
			$this->data['worldpay_merchant'] = $_POST['worldpay_merchant'];
		} else {
			$this->data['worldpay_merchant'] = $this->config->get('worldpay_merchant');
		}
		
		if (isset($_POST['worldpay_password'])) {
			$this->data['worldpay_password'] = $_POST['worldpay_password'];
		} else {
			$this->data['worldpay_password'] = $this->config->get('worldpay_password');
		}
		
		$this->data['callback'] = SITE_URL . 'index.php?route=payment/worldpay/callback';

		if (isset($_POST['worldpay_test'])) {
			$this->data['worldpay_test'] = $_POST['worldpay_test'];
		} else {
			$this->data['worldpay_test'] = $this->config->get('worldpay_test');
		}
		
		if (isset($_POST['worldpay_total'])) {
			$this->data['worldpay_total'] = $_POST['worldpay_total'];
		} else {
			$this->data['worldpay_total'] = $this->config->get('worldpay_total');
		}
				
		if (isset($_POST['worldpay_order_status_id'])) {
			$this->data['worldpay_order_status_id'] = $_POST['worldpay_order_status_id'];
		} else {
			$this->data['worldpay_order_status_id'] = $this->config->get('worldpay_order_status_id');
		}
		
		$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();
		
		if (isset($_POST['worldpay_geo_zone_id'])) {
			$this->data['worldpay_geo_zone_id'] = $_POST['worldpay_geo_zone_id'];
		} else {
			$this->data['worldpay_geo_zone_id'] = $this->config->get('worldpay_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		if (isset($_POST['worldpay_status'])) {
			$this->data['worldpay_status'] = $_POST['worldpay_status'];
		} else {
			$this->data['worldpay_status'] = $this->config->get('worldpay_status');
		}
		
		if (isset($_POST['worldpay_sort_order'])) {
			$this->data['worldpay_sort_order'] = $_POST['worldpay_sort_order'];
		} else {
			$this->data['worldpay_sort_order'] = $this->config->get('worldpay_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/worldpay')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['worldpay_merchant']) {
			$this->error['merchant'] = $this->_('error_merchant');
		}
		
		if (!$_POST['worldpay_password']) {
			$this->error['password'] = $this->_('error_password');
		}
		
		return $this->error ? false : true;
	}
}