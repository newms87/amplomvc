<?php
class ControllerShippingFree extends Controller {
	 
	
	public function index() {   
		$this->template->load('shipping/free');

		$this->load->language('shipping/free');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('free', $_POST);		
					
			$this->message->add('success', $this->_('text_success'));
						
			$this->redirect($this->url->link('extension/shipping'));
		}
				
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/free'));

		$this->data['action'] = $this->url->link('shipping/free');
		
		$this->data['cancel'] = $this->url->link('extension/shipping');
	
		if (isset($_POST['free_total'])) {
			$this->data['free_total'] = $_POST['free_total'];
		} else {
			$this->data['free_total'] = $this->config->get('free_total');
		}

		if (isset($_POST['free_geo_zone_id'])) {
			$this->data['free_geo_zone_id'] = $_POST['free_geo_zone_id'];
		} else {
			$this->data['free_geo_zone_id'] = $this->config->get('free_geo_zone_id');
		}
		
		if (isset($_POST['free_status'])) {
			$this->data['free_status'] = $_POST['free_status'];
		} else {
			$this->data['free_status'] = $this->config->get('free_status');
		}
		
		if (isset($_POST['free_sort_order'])) {
			$this->data['free_sort_order'] = $_POST['free_sort_order'];
		} else {
			$this->data['free_sort_order'] = $this->config->get('free_sort_order');
		}				
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
								
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/free')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}