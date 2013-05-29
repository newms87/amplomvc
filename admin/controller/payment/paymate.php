<?php
class ControllerPaymentPayMate extends Controller 
{
	

	public function index()
	{
		$this->template->load('payment/paymate');

		$this->load->language('payment/paymate');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paymate', $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/paymate'));

		$this->data['action'] = $this->url->link('payment/paymate');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['paymate_username'])) {
			$this->data['paymate_username'] = $_POST['paymate_username'];
		} else {
			$this->data['paymate_username'] = $this->config->get('paymate_username');
		}
		
		if (isset($_POST['paymate_password'])) {
			$this->data['paymate_username'] = $_POST['paymate_username'];
		} elseif ($this->config->get('paymate_password')) {
			$this->data['paymate_password'] = $this->config->get('paymate_password');
		} else {
			$this->data['paymate_password'] = md5(mt_rand());
		}
				
		if (isset($_POST['paymate_test'])) {
			$this->data['paymate_test'] = $_POST['paymate_test'];
		} else {
			$this->data['paymate_test'] = $this->config->get('paymate_test');
		}
				
		if (isset($_POST['paymate_total'])) {
			$this->data['paymate_total'] = $_POST['paymate_total'];
		} else {
			$this->data['paymate_total'] = $this->config->get('paymate_total');
		}
				
		if (isset($_POST['paymate_order_status_id'])) {
			$this->data['paymate_order_status_id'] = $_POST['paymate_order_status_id'];
		} else {
			$this->data['paymate_order_status_id'] = $this->config->get('paymate_order_status_id');
		}
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($_POST['paymate_geo_zone_id'])) {
			$this->data['paymate_geo_zone_id'] = $_POST['paymate_geo_zone_id'];
		} else {
			$this->data['paymate_geo_zone_id'] = $this->config->get('paymate_geo_zone_id');
		}
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['paymate_status'])) {
			$this->data['paymate_status'] = $_POST['paymate_status'];
		} else {
			$this->data['paymate_status'] = $this->config->get('paymate_status');
		}
		
		if (isset($_POST['paymate_sort_order'])) {
			$this->data['paymate_sort_order'] = $_POST['paymate_sort_order'];
		} else {
			$this->data['paymate_sort_order'] = $this->config->get('paymate_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/paymate')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['paymate_username']) {
			$this->error['username'] = $this->_('error_username');
		}
		
		if (!$_POST['paymate_password']) {
			$this->error['password'] = $this->_('error_password');
		}
						
		return $this->error ? false : true;
	}
}
