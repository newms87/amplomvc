<?php 
class ControllerPaymentSagepayUS extends Controller {
	 

	public function index() {
		$this->template->load('payment/sagepay_us');

		$this->load->language('payment/sagepay_us');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sagepay_us', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant_id'])) {
			$this->data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$this->data['error_merchant_id'] = '';
		}

 		if (isset($this->error['merchant_key'])) {
			$this->data['error_merchant_key'] = $this->error['merchant_key'];
		} else {
			$this->data['error_merchant_key'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/sagepay_us'));

		$this->data['action'] = $this->url->link('payment/sagepay_us');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['sagepay_us_merchant_id'])) {
			$this->data['sagepay_us_merchant_id'] = $_POST['sagepay_us_merchant_id'];
		} else {
			$this->data['sagepay_us_merchant_id'] = $this->config->get('sagepay_us_merchant_id');
		}
		
		if (isset($_POST['sagepay_us_merchant_key'])) {
			$this->data['sagepay_us_merchant_key'] = $_POST['sagepay_us_merchant_key'];
		} else {
			$this->data['sagepay_us_merchant_key'] = $this->config->get('sagepay_us_merchant_key');
		}
		
		if (isset($_POST['sagepay_us_total'])) {
			$this->data['sagepay_us_total'] = $_POST['sagepay_us_total'];
		} else {
			$this->data['sagepay_us_total'] = $this->config->get('sagepay_us_total'); 
		} 
		
		if (isset($_POST['sagepay_us_order_status_id'])) {
			$this->data['sagepay_us_order_status_id'] = $_POST['sagepay_us_order_status_id'];
		} else {
			$this->data['sagepay_us_order_status_id'] = $this->config->get('sagepay_us_order_status_id'); 
		} 

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['sagepay_us_geo_zone_id'])) {
			$this->data['sagepay_us_geo_zone_id'] = $_POST['sagepay_us_geo_zone_id'];
		} else {
			$this->data['sagepay_us_geo_zone_id'] = $this->config->get('sagepay_us_geo_zone_id'); 
		} 
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['sagepay_us_status'])) {
			$this->data['sagepay_us_status'] = $_POST['sagepay_us_status'];
		} else {
			$this->data['sagepay_us_status'] = $this->config->get('sagepay_us_status');
		}
		
		if (isset($_POST['sagepay_us_sort_order'])) {
			$this->data['sagepay_us_sort_order'] = $_POST['sagepay_us_sort_order'];
		} else {
			$this->data['sagepay_us_sort_order'] = $this->config->get('sagepay_us_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/sagepay_us')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['sagepay_us_merchant_id']) {
			$this->error['merchant_id'] = $this->_('error_merchant_id');
		}

		if (!$_POST['sagepay_us_merchant_key']) {
			$this->error['merchant_key'] = $this->_('error_merchant_key');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}