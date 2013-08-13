<?php
class Admin_Controller_Payment_Alertpay extends Controller
{
	

	public function index()
	{
		$this->template->load('payment/alertpay');

		$this->language->load('payment/alertpay');

		$this->document->setTitle($this->_('head_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('alertpay', $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

  		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

 		if (isset($this->error['security'])) {
			$this->data['error_security'] = $this->error['security'];
		} else {
			$this->data['error_security'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/alertpay'));

		$this->data['action'] = $this->url->link('payment/alertpay');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['alertpay_merchant'])) {
			$this->data['alertpay_merchant'] = $_POST['alertpay_merchant'];
		} else {
			$this->data['alertpay_merchant'] = $this->config->get('alertpay_merchant');
		}

		if (isset($_POST['alertpay_security'])) {
			$this->data['alertpay_security'] = $_POST['alertpay_security'];
		} else {
			$this->data['alertpay_security'] = $this->config->get('alertpay_security');
		}
		
		$this->data['callback'] = $this->url->link('payment/alertpay/callback');
		
		if (isset($_POST['alertpay_total'])) {
			$this->data['alertpay_total'] = $_POST['alertpay_total'];
		} else {
			$this->data['alertpay_total'] = $this->config->get('alertpay_total');
		}
				
		if (isset($_POST['alertpay_order_status_id'])) {
			$this->data['alertpay_order_status_id'] = $_POST['alertpay_order_status_id'];
		} else {
			$this->data['alertpay_order_status_id'] = $this->config->get('alertpay_order_status_id');
		}
		
		$this->data['order_statuses'] = $this->order->getOrderStatuses();
		
		if (isset($_POST['alertpay_geo_zone_id'])) {
			$this->data['alertpay_geo_zone_id'] = $_POST['alertpay_geo_zone_id'];
		} else {
			$this->data['alertpay_geo_zone_id'] = $this->config->get('alertpay_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		if (isset($_POST['alertpay_status'])) {
			$this->data['alertpay_status'] = $_POST['alertpay_status'];
		} else {
			$this->data['alertpay_status'] = $this->config->get('alertpay_status');
		}
		
		if (isset($_POST['alertpay_sort_order'])) {
			$this->data['alertpay_sort_order'] = $_POST['alertpay_sort_order'];
		} else {
			$this->data['alertpay_sort_order'] = $this->config->get('alertpay_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/alertpay')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['alertpay_merchant']) {
			$this->error['merchant'] = $this->_('error_merchant');
		}

		if (!$_POST['alertpay_security']) {
			$this->error['security'] = $this->_('error_security');
		}
		
		return $this->error ? false : true;
	}
}