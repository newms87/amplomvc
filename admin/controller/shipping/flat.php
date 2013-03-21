<?php
class ControllerShippingFlat extends Controller {
	 
	
	public function index() {   
$this->template->load('shipping/flat');

		$this->load->language('shipping/flat');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('flat', $_POST);		
					
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/flat'));

		$this->data['action'] = $this->url->link('shipping/flat');
		
		$this->data['cancel'] = $this->url->link('extension/shipping');
		
		if (isset($_POST['flat_cost'])) {
			$this->data['flat_cost'] = $_POST['flat_cost'];
		} else {
			$this->data['flat_cost'] = $this->config->get('flat_cost');
		}

		if (isset($_POST['flat_tax_class_id'])) {
			$this->data['flat_tax_class_id'] = $_POST['flat_tax_class_id'];
		} else {
			$this->data['flat_tax_class_id'] = $this->config->get('flat_tax_class_id');
		}

		if (isset($_POST['flat_geo_zone_id'])) {
			$this->data['flat_geo_zone_id'] = $_POST['flat_geo_zone_id'];
		} else {
			$this->data['flat_geo_zone_id'] = $this->config->get('flat_geo_zone_id');
		}
		
		if (isset($_POST['flat_status'])) {
			$this->data['flat_status'] = $_POST['flat_status'];
		} else {
			$this->data['flat_status'] = $this->config->get('flat_status');
		}
		
		if (isset($_POST['flat_sort_order'])) {
			$this->data['flat_sort_order'] = $_POST['flat_sort_order'];
		} else {
			$this->data['flat_sort_order'] = $this->config->get('flat_sort_order');
		}				

		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
								
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/flat')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}