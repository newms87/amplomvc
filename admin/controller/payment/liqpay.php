<?php
class Admin_Controller_Payment_Liqpay extends Controller
{
	

	public function index()
	{
		$this->template->load('payment/liqpay');

		$this->language->load('payment/liqpay');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('liqpay', $_POST);
			
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
		
		if (isset($this->error['signature'])) {
			$this->data['error_signature'] = $this->error['signature'];
		} else {
			$this->data['error_signature'] = '';
		}
		
		if (isset($this->error['type'])) {
			$this->data['error_type'] = $this->error['type'];
		} else {
			$this->data['error_type'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/liqpay'));

		$this->data['action'] = $this->url->link('payment/liqpay');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['liqpay_merchant'])) {
			$this->data['liqpay_merchant'] = $_POST['liqpay_merchant'];
		} else {
			$this->data['liqpay_merchant'] = $this->config->get('liqpay_merchant');
		}

		if (isset($_POST['liqpay_signature'])) {
			$this->data['liqpay_signature'] = $_POST['liqpay_signature'];
		} else {
			$this->data['liqpay_signature'] = $this->config->get('liqpay_signature');
		}

		if (isset($_POST['liqpay_type'])) {
			$this->data['liqpay_type'] = $_POST['liqpay_type'];
		} else {
			$this->data['liqpay_type'] = $this->config->get('liqpay_type');
		}
		
		if (isset($_POST['liqpay_total'])) {
			$this->data['liqpay_total'] = $_POST['liqpay_total'];
		} else {
			$this->data['liqpay_total'] = $this->config->get('liqpay_total');
		}
				
		if (isset($_POST['liqpay_order_status_id'])) {
			$this->data['liqpay_order_status_id'] = $_POST['liqpay_order_status_id'];
		} else {
			$this->data['liqpay_order_status_id'] = $this->config->get('liqpay_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();
		
		if (isset($_POST['liqpay_geo_zone_id'])) {
			$this->data['liqpay_geo_zone_id'] = $_POST['liqpay_geo_zone_id'];
		} else {
			$this->data['liqpay_geo_zone_id'] = $this->config->get('liqpay_geo_zone_id');
		}
		
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		if (isset($_POST['liqpay_status'])) {
			$this->data['liqpay_status'] = $_POST['liqpay_status'];
		} else {
			$this->data['liqpay_status'] = $this->config->get('liqpay_status');
		}
		
		if (isset($_POST['liqpay_sort_order'])) {
			$this->data['liqpay_sort_order'] = $_POST['liqpay_sort_order'];
		} else {
			$this->data['liqpay_sort_order'] = $this->config->get('liqpay_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/liqpay')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['liqpay_merchant']) {
			$this->error['merchant'] = $this->_('error_merchant');
		}

		if (!$_POST['liqpay_signature']) {
			$this->error['signature'] = $this->_('error_signature');
		}
		
		return $this->error ? false : true;
	}
}