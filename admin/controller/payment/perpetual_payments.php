<?php 
class ControllerPaymentPerpetualPayments extends Controller {
	 

	public function index() {
$this->template->load('payment/perpetual_payments');

		$this->load->language('payment/perpetual_payments');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('perpetual_payments', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['auth_id'])) {
			$this->data['error_auth_id'] = $this->error['auth_id'];
		} else {
			$this->data['error_auth_id'] = '';
		}
		
 		if (isset($this->error['auth_pass'])) {
			$this->data['error_auth_pass'] = $this->error['auth_pass'];
		} else {
			$this->data['error_auth_pass'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/perpetual_payments'));

		$this->data['action'] = $this->url->link('payment/perpetual_payments');
		
		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['perpetual_payments_auth_id'])) {
			$this->data['perpetual_payments_auth_id'] = $_POST['perpetual_payments_auth_id'];
		} else {
			$this->data['perpetual_payments_auth_id'] = $this->config->get('perpetual_payments_auth_id');
		}
		
		if (isset($_POST['perpetual_payments_auth_pass'])) {
			$this->data['perpetual_payments_auth_pass'] = $_POST['perpetual_payments_auth_pass'];
		} else {
			$this->data['perpetual_payments_auth_pass'] = $this->config->get('perpetual_payments_auth_pass');
		}
		
		if (isset($_POST['perpetual_payments_test'])) {
			$this->data['perpetual_payments_test'] = $_POST['perpetual_payments_test'];
		} else {
			$this->data['perpetual_payments_test'] = $this->config->get('perpetual_payments_test');
		}
		
		if (isset($_POST['perpetual_payments_total'])) {
			$this->data['perpetual_payments_total'] = $_POST['perpetual_payments_total'];
		} else {
			$this->data['perpetual_payments_total'] = $this->config->get('perpetual_payments_total'); 
		} 
				
		if (isset($_POST['perpetual_payments_order_status_id'])) {
			$this->data['perpetual_payments_order_status_id'] = $_POST['perpetual_payments_order_status_id'];
		} else {
			$this->data['perpetual_payments_order_status_id'] = $this->config->get('perpetual_payments_order_status_id'); 
		} 

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['perpetual_payments_geo_zone_id'])) {
			$this->data['perpetual_payments_geo_zone_id'] = $_POST['perpetual_payments_geo_zone_id'];
		} else {
			$this->data['perpetual_payments_geo_zone_id'] = $this->config->get('perpetual_payments_geo_zone_id'); 
		} 
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['perpetual_payments_status'])) {
			$this->data['perpetual_payments_status'] = $_POST['perpetual_payments_status'];
		} else {
			$this->data['perpetual_payments_status'] = $this->config->get('perpetual_payments_status');
		}
		
		if (isset($_POST['perpetual_payments_sort_order'])) {
			$this->data['perpetual_payments_sort_order'] = $_POST['perpetual_payments_sort_order'];
		} else {
			$this->data['perpetual_payments_sort_order'] = $this->config->get('perpetual_payments_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/perpetual_payments')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['perpetual_payments_auth_id']) {
			$this->error['auth_id'] = $this->_('error_auth_id');
		}

		if (!$_POST['perpetual_payments_auth_pass']) {
			$this->error['auth_pass'] = $this->_('error_auth_pass');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}