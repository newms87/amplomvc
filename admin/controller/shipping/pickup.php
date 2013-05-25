<?php
class ControllerShippingPickup extends Controller {
	
	
	public function index() {	
		$this->template->load('shipping/pickup');

		$this->load->language('shipping/pickup');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pickup', $_POST);		
					
			$this->message->add('success', $this->_('text_success'));
						
			$this->url->redirect($this->url->link('extension/shipping'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/pickup'));

		$this->data['action'] = $this->url->link('shipping/pickup');
		
		$this->data['cancel'] = $this->url->link('extension/shipping');

		if (isset($_POST['pickup_geo_zone_id'])) {
			$this->data['pickup_geo_zone_id'] = $_POST['pickup_geo_zone_id'];
		} else {
			$this->data['pickup_geo_zone_id'] = $this->config->get('pickup_geo_zone_id');
		}
		
		if (isset($_POST['pickup_status'])) {
			$this->data['pickup_status'] = $_POST['pickup_status'];
		} else {
			$this->data['pickup_status'] = $this->config->get('pickup_status');
		}
		
		if (isset($_POST['pickup_sort_order'])) {
			$this->data['pickup_sort_order'] = $_POST['pickup_sort_order'];
		} else {
			$this->data['pickup_sort_order'] = $this->config->get('pickup_sort_order');
		}				
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
						
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/pickup')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;	
	}
}