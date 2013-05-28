<?php
class ControllerShippingItem extends Controller {
	
	
	public function index() {
		$this->template->load('shipping/item');

		$this->load->language('shipping/item');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('item', $_POST);

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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/item'));

		$this->data['action'] = $this->url->link('shipping/item');
		
		$this->data['cancel'] = $this->url->link('extension/shipping');

		if (isset($_POST['item_cost'])) {
			$this->data['item_cost'] = $_POST['item_cost'];
		} else {
			$this->data['item_cost'] = $this->config->get('item_cost');
		}

		if (isset($_POST['item_tax_class_id'])) {
			$this->data['item_tax_class_id'] = $_POST['item_tax_class_id'];
		} else {
			$this->data['item_tax_class_id'] = $this->config->get('item_tax_class_id');
		}
				
		if (isset($_POST['item_geo_zone_id'])) {
			$this->data['item_geo_zone_id'] = $_POST['item_geo_zone_id'];
		} else {
			$this->data['item_geo_zone_id'] = $this->config->get('item_geo_zone_id');
		}
		
		if (isset($_POST['item_status'])) {
			$this->data['item_status'] = $_POST['item_status'];
		} else {
			$this->data['item_status'] = $this->config->get('item_status');
		}
		
		if (isset($_POST['item_sort_order'])) {
			$this->data['item_sort_order'] = $_POST['item_sort_order'];
		} else {
			$this->data['item_sort_order'] = $this->config->get('item_sort_order');
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
		if (!$this->user->hasPermission('modify', 'shipping/item')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}