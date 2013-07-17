<?php
class Admin_Controller_Payment_Cod extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('payment/cod');

		$this->language->load('payment/cod');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('cod', $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/payment'));
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
		
		$this->data['order_statuses'] = $this->order->getOrderStatuses();
		
		if (isset($_POST['cod_geo_zone_id'])) {
			$this->data['cod_geo_zone_id'] = $_POST['cod_geo_zone_id'];
		} else {
			$this->data['cod_geo_zone_id'] = $this->config->get('cod_geo_zone_id');
		}
		
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
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
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/cod')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}