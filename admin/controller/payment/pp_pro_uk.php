<?php
class Admin_Controller_Payment_PpProUk extends Controller 
{
	

	public function index()
	{
		$this->template->load('payment/pp_pro_uk');

		$this->language->load('payment/pp_pro_uk');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('pp_pro_uk', $_POST);
			
			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['vendor'])) {
			$this->data['error_vendor'] = $this->error['vendor'];
		} else {
			$this->data['error_vendor'] = '';
		}

 		if (isset($this->error['user'])) {
			$this->data['error_user'] = $this->error['user'];
		} else {
			$this->data['error_user'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

 		if (isset($this->error['partner'])) {
			$this->data['error_partner'] = $this->error['partner'];
		} else {
			$this->data['error_partner'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/pp_pro_uk'));

		$this->data['action'] = $this->url->link('payment/pp_pro_uk');
		
		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['pp_pro_uk_vendor'])) {
			$this->data['pp_pro_uk_vendor'] = $_POST['pp_pro_uk_vendor'];
		} else {
			$this->data['pp_pro_uk_vendor'] = $this->config->get('pp_pro_uk_vendor');
		}
		
		if (isset($_POST['pp_pro_uk_user'])) {
			$this->data['pp_pro_uk_user'] = $_POST['pp_pro_uk_user'];
		} else {
			$this->data['pp_pro_uk_user'] = $this->config->get('pp_pro_uk_user');
		}
		
		if (isset($_POST['pp_pro_uk_password'])) {
			$this->data['pp_pro_uk_password'] = $_POST['pp_pro_uk_password'];
		} else {
			$this->data['pp_pro_uk_password'] = $this->config->get('pp_pro_uk_password');
		}
		
		if (isset($_POST['pp_pro_uk_partner'])) {
			$this->data['pp_pro_uk_partner'] = $_POST['pp_pro_uk_partner'];
		} elseif ($this->config->has('pp_pro_uk_partner')) {
			$this->data['pp_pro_uk_partner'] = $this->config->get('pp_pro_uk_partner');
		} else {
			$this->data['pp_pro_uk_partner'] = 'PayPal';
		}
		
		if (isset($_POST['pp_pro_uk_test'])) {
			$this->data['pp_pro_uk_test'] = $_POST['pp_pro_uk_test'];
		} else {
			$this->data['pp_pro_uk_test'] = $this->config->get('pp_pro_uk_test');
		}
		
		if (isset($_POST['pp_pro_uk_method'])) {
			$this->data['pp_pro_uk_transaction'] = $_POST['pp_pro_uk_transaction'];
		} else {
			$this->data['pp_pro_uk_transaction'] = $this->config->get('pp_pro_uk_transaction');
		}
		
		if (isset($_POST['pp_pro_uk_total'])) {
			$this->data['pp_pro_uk_total'] = $_POST['pp_pro_uk_total'];
		} else {
			$this->data['pp_pro_uk_total'] = $this->config->get('pp_pro_uk_total');
		}
				
		if (isset($_POST['pp_pro_uk_order_status_id'])) {
			$this->data['pp_pro_uk_order_status_id'] = $_POST['pp_pro_uk_order_status_id'];
		} else {
			$this->data['pp_pro_uk_order_status_id'] = $this->config->get('pp_pro_uk_order_status_id');
		}

		$this->data['order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();
		
		if (isset($_POST['pp_pro_uk_geo_zone_id'])) {
			$this->data['pp_pro_uk_geo_zone_id'] = $_POST['pp_pro_uk_geo_zone_id'];
		} else {
			$this->data['pp_pro_uk_geo_zone_id'] = $this->config->get('pp_pro_uk_geo_zone_id');
		}
		
		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();
		
		if (isset($_POST['pp_pro_uk_status'])) {
			$this->data['pp_pro_uk_status'] = $_POST['pp_pro_uk_status'];
		} else {
			$this->data['pp_pro_uk_status'] = $this->config->get('pp_pro_uk_status');
		}
		
		if (isset($_POST['pp_pro_uk_sort_order'])) {
			$this->data['pp_pro_uk_sort_order'] = $_POST['pp_pro_uk_sort_order'];
		} else {
			$this->data['pp_pro_uk_sort_order'] = $this->config->get('pp_pro_uk_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/pp_pro_uk')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['pp_pro_uk_vendor']) {
			$this->error['vendor'] = $this->_('error_vendor');
		}
		
		if (!$_POST['pp_pro_uk_user']) {
			$this->error['user'] = $this->_('error_user');
		}

		if (!$_POST['pp_pro_uk_password']) {
			$this->error['password'] = $this->_('error_password');
		}

		if (!$_POST['pp_pro_uk_partner']) {
			$this->error['partner'] = $this->_('error_partner');
		}
		
		return $this->error ? false : true;
	}
}