<?php 
class ControllerPaymentTwoCheckout extends Controller {
	 

	public function index() {
		$this->template->load('payment/twocheckout');

		$this->load->language('payment/twocheckout');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('twocheckout', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['account'])) {
			$this->data['error_account'] = $this->error['account'];
		} else {
			$this->data['error_account'] = '';
		}	
		
		if (isset($this->error['secret'])) {
			$this->data['error_secret'] = $this->error['secret'];
		} else {
			$this->data['error_secret'] = '';
		}	
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/twocheckout'));

		$this->data['action'] = $this->url->link('payment/twocheckout');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['twocheckout_account'])) {
			$this->data['twocheckout_account'] = $_POST['twocheckout_account'];
		} else {
			$this->data['twocheckout_account'] = $this->config->get('twocheckout_account');
		}

		if (isset($_POST['twocheckout_secret'])) {
			$this->data['twocheckout_secret'] = $_POST['twocheckout_secret'];
		} else {
			$this->data['twocheckout_secret'] = $this->config->get('twocheckout_secret');
		}
		
		if (isset($_POST['twocheckout_test'])) {
			$this->data['twocheckout_test'] = $_POST['twocheckout_test'];
		} else {
			$this->data['twocheckout_test'] = $this->config->get('twocheckout_test');
		}
		
		if (isset($_POST['twocheckout_total'])) {
			$this->data['twocheckout_total'] = $_POST['twocheckout_total'];
		} else {
			$this->data['twocheckout_total'] = $this->config->get('twocheckout_total'); 
		} 
				
		if (isset($_POST['twocheckout_order_status_id'])) {
			$this->data['twocheckout_order_status_id'] = $_POST['twocheckout_order_status_id'];
		} else {
			$this->data['twocheckout_order_status_id'] = $this->config->get('twocheckout_order_status_id'); 
		}
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['twocheckout_geo_zone_id'])) {
			$this->data['twocheckout_geo_zone_id'] = $_POST['twocheckout_geo_zone_id'];
		} else {
			$this->data['twocheckout_geo_zone_id'] = $this->config->get('twocheckout_geo_zone_id'); 
		}
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['twocheckout_status'])) {
			$this->data['twocheckout_status'] = $_POST['twocheckout_status'];
		} else {
			$this->data['twocheckout_status'] = $this->config->get('twocheckout_status');
		}
		
		if (isset($_POST['twocheckout_sort_order'])) {
			$this->data['twocheckout_sort_order'] = $_POST['twocheckout_sort_order'];
		} else {
			$this->data['twocheckout_sort_order'] = $this->config->get('twocheckout_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/twocheckout')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['twocheckout_account']) {
			$this->error['account'] = $this->_('error_account');
		}

		if (!$_POST['twocheckout_secret']) {
			$this->error['secret'] = $this->_('error_secret');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}