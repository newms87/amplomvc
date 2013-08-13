<?php
class Admin_Controller_Payment_Cod extends Controller
{
	public function index()
	{
		//Template and Language
		$this->template->load('payment/cod');
		$this->language->load('payment/cod');

		//Edit Settings
		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('cod', $_POST);

			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('extension/payment'));
		}
		
		//Page Head
		$this->document->setTitle($this->_('head_title'));
		
		//Breadcrumbs
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/cod'));
		
		//Load Information
		if (!$this->request->isPost()) {
			$cod_info = $this->config->loadGroup('cod');
		}
		
		//Set Values or Defaults
		$defaults = array(
			'cod_total' => '',
			'cod_order_status_id' => '',
			'cod_geo_zone_id' => '',
			'cod_status' => '',
			'cod_sort_order' => '',
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($cod_info[$key])) {
				$this->data[$key] = $cod_info[$key];
			} else {
				$this->data[$key] = $default;
			}
		}
		
		//Additional Data
		$this->data['data_order_statuses'] = $this->order->getOrderStatuses();
		$this->data['data_geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		//Action Buttons
		$this->data['save'] = $this->url->link('payment/cod');
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		//Render
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