<?php 
class ControllerPaymentCheque extends Controller {
	 

	public function index() {
		$this->template->load('payment/cheque');

		$this->load->language('payment/cheque');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cheque', $_POST);				
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['payable'])) {
			$this->data['error_payable'] = $this->error['payable'];
		} else {
			$this->data['error_payable'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/cheque'));

		$this->data['action'] = $this->url->link('payment/cheque');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['cheque_payable'])) {
			$this->data['cheque_payable'] = $_POST['cheque_payable'];
		} else {
			$this->data['cheque_payable'] = $this->config->get('cheque_payable');
		}
		
		if (isset($_POST['cheque_total'])) {
			$this->data['cheque_total'] = $_POST['cheque_total'];
		} else {
			$this->data['cheque_total'] = $this->config->get('cheque_total'); 
		} 
				
		if (isset($_POST['cheque_order_status_id'])) {
			$this->data['cheque_order_status_id'] = $_POST['cheque_order_status_id'];
		} else {
			$this->data['cheque_order_status_id'] = $this->config->get('cheque_order_status_id'); 
		} 
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['cheque_geo_zone_id'])) {
			$this->data['cheque_geo_zone_id'] = $_POST['cheque_geo_zone_id'];
		} else {
			$this->data['cheque_geo_zone_id'] = $this->config->get('cheque_geo_zone_id'); 
		} 
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['cheque_status'])) {
			$this->data['cheque_status'] = $_POST['cheque_status'];
		} else {
			$this->data['cheque_status'] = $this->config->get('cheque_status');
		}
		
		if (isset($_POST['cheque_sort_order'])) {
			$this->data['cheque_sort_order'] = $_POST['cheque_sort_order'];
		} else {
			$this->data['cheque_sort_order'] = $this->config->get('cheque_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/cheque')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['cheque_payable']) {
			$this->error['payable'] = $this->_('error_payable');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}