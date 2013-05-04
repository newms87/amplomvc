<?php 
class ControllerPaymentCod extends Controller {
	 
	 
	public function index() { 
		$this->template->load('payment/cod');

		$this->load->language('payment/cod');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cod', $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->redirect($this->url->link('extension/payment'));
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/cod'));

		$this->data['action'] = $this->url->link('payment/cod');

		$this->data['cancel'] = $this->url->link('extension/payment');	
		
		if (isset($_POST['cod_total'])) {
			$this->data['cod_total'] = $_POST['cod_total'];
		} else {
			$this->data['cod_total'] = $this->config->get('cod_total'); 
		}
				
		if (isset($_POST['cod_order_status_id'])) {
			$this->data['cod_order_status_id'] = $_POST['cod_order_status_id'];
		} else {
			$this->data['cod_order_status_id'] = $this->config->get('cod_order_status_id'); 
		} 
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($_POST['cod_geo_zone_id'])) {
			$this->data['cod_geo_zone_id'] = $_POST['cod_geo_zone_id'];
		} else {
			$this->data['cod_geo_zone_id'] = $this->config->get('cod_geo_zone_id'); 
		} 
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($_POST['cod_status'])) {
			$this->data['cod_status'] = $_POST['cod_status'];
		} else {
			$this->data['cod_status'] = $this->config->get('cod_status');
		}
		
		if (isset($_POST['cod_sort_order'])) {
			$this->data['cod_sort_order'] = $_POST['cod_sort_order'];
		} else {
			$this->data['cod_sort_order'] = $this->config->get('cod_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/cod')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}