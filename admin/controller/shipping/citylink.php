<?php
class Admin_Controller_Shipping_Citylink extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('shipping/citylink');

		$this->load->language('shipping/citylink');

		$this->document->setTitle($this->_('heading_title'));
		
		if (($this->request->isPost()) && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('citylink', $_POST);
					
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
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/citylink'));

		$this->data['action'] = $this->url->link('shipping/citylink');
		
		$this->data['cancel'] = $this->url->link('extension/shipping');
		
		if (isset($_POST['citylink_rate'])) {
			$this->data['citylink_rate'] = $_POST['citylink_rate'];
		} elseif ($this->config->get('citylink_rate')) {
			$this->data['citylink_rate'] = $this->config->get('citylink_rate');
		} else {
			$this->data['citylink_rate'] = '10:11.6,15:14.1,20:16.60,25:19.1,30:21.6,35:24.1,40:26.6,45:29.1,50:31.6,55:34.1,60:36.6,65:39.1,70:41.6,75:44.1,80:46.6,100:56.6,125:69.1,150:81.6,200:106.6';
		}

		if (isset($_POST['citylink_tax_class_id'])) {
			$this->data['citylink_tax_class_id'] = $_POST['citylink_tax_class_id'];
		} else {
			$this->data['citylink_tax_class_id'] = $this->config->get('citylink_tax_class_id');
		}

		if (isset($_POST['citylink_geo_zone_id'])) {
			$this->data['citylink_geo_zone_id'] = $_POST['citylink_geo_zone_id'];
		} else {
			$this->data['citylink_geo_zone_id'] = $this->config->get('citylink_geo_zone_id');
		}
		
		if (isset($_POST['citylink_status'])) {
			$this->data['citylink_status'] = $_POST['citylink_status'];
		} else {
			$this->data['citylink_status'] = $this->config->get('citylink_status');
		}
		
		if (isset($_POST['citylink_sort_order'])) {
			$this->data['citylink_sort_order'] = $_POST['citylink_sort_order'];
		} else {
			$this->data['citylink_sort_order'] = $this->config->get('citylink_sort_order');
		}

		$this->data['tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();
		
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'shipping/citylink')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		return $this->error ? false : true;
	}
}