<?php 
class ControllerPaymentSagepayDirect extends Controller {
	 

	public function index() {
$this->template->load('payment/sagepay_direct');

		$this->load->language('payment/sagepay_direct');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sagepay_direct', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['vendor'])) {
			$this->data['error_vendor'] = $this->error['vendor'];
		} else {
			$this->data['error_vendor'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/sagepay_direct'));

		$this->data['action'] = $this->url->link('payment/sagepay_direct');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['sagepay_direct_vendor'])) {
			$this->data['sagepay_direct_vendor'] = $_POST['sagepay_direct_vendor'];
		} else {
			$this->data['sagepay_direct_vendor'] = $this->config->get('sagepay_direct_vendor');
		}
		
		if (isset($_POST['sagepay_direct_password'])) {
			$this->data['sagepay_direct_password'] = $_POST['sagepay_direct_password'];
		} else {
			$this->data['sagepay_direct_password'] = $this->config->get('sagepay_direct_password');
		}


		if (isset($_POST['sagepay_direct_test'])) {
			$this->data['sagepay_direct_test'] = $_POST['sagepay_direct_test'];
		} else {
			$this->data['sagepay_direct_test'] = $this->config->get('sagepay_direct_test');
		}
		
		if (isset($_POST['sagepay_direct_transaction'])) {
			$this->data['sagepay_direct_transaction'] = $_POST['sagepay_direct_transaction'];
		} else {
			$this->data['sagepay_direct_transaction'] = $this->config->get('sagepay_direct_transaction');
		}
		
		if (isset($_POST['sagepay_direct_total'])) {
			$this->data['sagepay_direct_total'] = $_POST['sagepay_direct_total'];
		} else {
			$this->data['sagepay_direct_total'] = $this->config->get('sagepay_direct_total'); 
		} 
				
		if (isset($_POST['sagepay_direct_order_status_id'])) {
			$this->data['sagepay_direct_order_status_id'] = $_POST['sagepay_direct_order_status_id'];
		} else {
			$this->data['sagepay_direct_order_status_id'] = $this->config->get('sagepay_direct_order_status_id'); 
		} 

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['sagepay_direct_geo_zone_id'])) {
			$this->data['sagepay_direct_geo_zone_id'] = $_POST['sagepay_direct_geo_zone_id'];
		} else {
			$this->data['sagepay_direct_geo_zone_id'] = $this->config->get('sagepay_direct_geo_zone_id'); 
		} 
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['sagepay_direct_status'])) {
			$this->data['sagepay_direct_status'] = $_POST['sagepay_direct_status'];
		} else {
			$this->data['sagepay_direct_status'] = $this->config->get('sagepay_direct_status');
		}
		
		if (isset($_POST['sagepay_direct_sort_order'])) {
			$this->data['sagepay_direct_sort_order'] = $_POST['sagepay_direct_sort_order'];
		} else {
			$this->data['sagepay_direct_sort_order'] = $this->config->get('sagepay_direct_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/sagepay_direct')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['sagepay_direct_vendor']) {
			$this->error['vendor'] = $this->_('error_vendor');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}