<?php
class Admin_Controller_Payment_Moneybookers extends Controller 
{
	
	
	public function index()
	{
		$this->template->load('payment/moneybookers');

		$this->load->language('payment/moneybookers');
		
		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('moneybookers', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
		
			$this->url->redirect($this->url->link('extension/payment'));
		}
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
 		
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/moneybookers'));

		$this->data['action'] = $this->url->link('payment/moneybookers');
		
		$this->data['cancel'] = $this->url->link('extension/payment');
		
		if (isset($_POST['moneybookers_email'])) {
			$this->data['moneybookers_email'] = $_POST['moneybookers_email'];
		} else {
			$this->data['moneybookers_email'] = $this->config->get('moneybookers_email');
		}
		
		if (isset($_POST['moneybookers_secret'])) {
			$this->data['moneybookers_secret'] = $_POST['moneybookers_secret'];
		} else {
			$this->data['moneybookers_secret'] = $this->config->get('moneybookers_secret');
		}
		
		if (isset($_POST['moneybookers_total'])) {
			$this->data['moneybookers_total'] = $_POST['moneybookers_total'];
		} else {
			$this->data['moneybookers_total'] = $this->config->get('moneybookers_total');
		}
				
		if (isset($_POST['moneybookers_order_status_id'])) {
			$this->data['moneybookers_order_status_id'] = $_POST['moneybookers_order_status_id'];
		} else {
			$this->data['moneybookers_order_status_id'] = $this->config->get('moneybookers_order_status_id');
		}

		if (isset($_POST['moneybookers_pending_status_id'])) {
			$this->data['moneybookers_pending_status_id'] = $_POST['moneybookers_pending_status_id'];
		} else {
			$this->data['moneybookers_pending_status_id'] = $this->config->get('moneybookers_pending_status_id');
		}

		if (isset($_POST['moneybookers_canceled_status_id'])) {
			$this->data['moneybookers_canceled_status_id'] = $_POST['moneybookers_canceled_status_id'];
		} else {
			$this->data['moneybookers_canceled_status_id'] = $this->config->get('moneybookers_canceled_status_id');
		}

		if (isset($_POST['moneybookers_failed_status_id'])) {
			$this->data['moneybookers_failed_status_id'] = $_POST['moneybookers_failed_status_id'];
		} else {
			$this->data['moneybookers_failed_status_id'] = $this->config->get('moneybookers_failed_status_id');
		}

		if (isset($_POST['moneybookers_chargeback_status_id'])) {
			$this->data['moneybookers_chargeback_status_id'] = $_POST['moneybookers_chargeback_status_id'];
		} else {
			$this->data['moneybookers_chargeback_status_id'] = $this->config->get('moneybookers_chargeback_status_id');
		}
		
		$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();
		
		if (isset($_POST['moneybookers_geo_zone_id'])) {
			$this->data['moneybookers_geo_zone_id'] = $_POST['moneybookers_geo_zone_id'];
		} else {
			$this->data['moneybookers_geo_zone_id'] = $this->config->get('moneybookers_geo_zone_id');
		}
		
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		if (isset($_POST['moneybookers_status'])) {
			$this->data['moneybookers_status'] = $_POST['moneybookers_status'];
		} else {
			$this->data['moneybookers_status'] = $this->config->get('moneybookers_status');
		}
		
		if (isset($_POST['moneybookers_sort_order'])) {
			$this->data['moneybookers_sort_order'] = $_POST['moneybookers_sort_order'];
		} else {
			$this->data['moneybookers_sort_order'] = $this->config->get('moneybookers_sort_order');
		}
		
		if (isset($_POST['moneybookers_rid'])) {
			$this->data['moneybookers_rid'] = $_POST['moneybookers_rid'];
		} else {
			$this->data['moneybookers_rid'] = $this->config->get('moneybookers_rid');
		}
		
		if (isset($_POST['moneybookers_custnote'])) {
			$this->data['moneybookers_custnote'] = $_POST['moneybookers_custnote'];
		} else {
			$this->data['moneybookers_custnote'] = $this->config->get('moneybookers_custnote');
		}

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/moneybookers')) {
			$this->error['warning'] = $this->_('error_permission');
		}
		
		if (!$_POST['moneybookers_email']) {
			$this->error['email'] = $this->_('error_email');
		}
				
		return $this->error ? false : true;
	}
}