<?php
class Admin_Controller_Payment_WebPaymentSoftware extends Controller
{


	public function index()
	{
		$this->template->load('payment/web_payment_software');

		$this->language->load('payment/web_payment_software');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && ($this->validate())) {
			$this->System_Model_Setting->editSetting('web_payment_software', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/payment');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['login'])) {
			$this->data['error_login'] = $this->error['login'];
		} else {
			$this->data['error_login'] = '';
		}

		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/web_payment_software'));

		$this->data['action'] = $this->url->link('payment/web_payment_software');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['web_payment_software_login'])) {
			$this->data['web_payment_software_merchant_name'] = $_POST['web_payment_software_merchant_name'];
		} else {
			$this->data['web_payment_software_merchant_name'] = $this->config->get('web_payment_software_merchant_name');
		}

		if (isset($_POST['web_payment_software_merchant_key'])) {
			$this->data['web_payment_software_merchant_key'] = $_POST['web_payment_software_merchant_key'];
		} else {
			$this->data['web_payment_software_merchant_key'] = $this->config->get('web_payment_software_merchant_key');
		}

		if (isset($_POST['web_payment_software_mode'])) {
			$this->data['web_payment_software_mode'] = $_POST['web_payment_software_mode'];
		} else {
			$this->data['web_payment_software_mode'] = $this->config->get('web_payment_software_mode');
		}

		if (isset($_POST['web_payment_software_method'])) {
			$this->data['web_payment_software_method'] = $_POST['web_payment_software_method'];
		} else {
			$this->data['web_payment_software_method'] = $this->config->get('web_payment_software_method');
		}

		if (isset($_POST['web_payment_software_order_status_id'])) {
			$this->data['web_payment_software_order_status_id'] = $_POST['web_payment_software_order_status_id'];
		} else {
			$this->data['web_payment_software_order_status_id'] = $this->config->get('web_payment_software_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['web_payment_software_geo_zone_id'])) {
			$this->data['web_payment_software_geo_zone_id'] = $_POST['web_payment_software_geo_zone_id'];
		} else {
			$this->data['web_payment_software_geo_zone_id'] = $this->config->get('web_payment_software_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['web_payment_software_status'])) {
			$this->data['web_payment_software_status'] = $_POST['web_payment_software_status'];
		} else {
			$this->data['web_payment_software_status'] = $this->config->get('web_payment_software_status');
		}

		if (isset($_POST['web_payment_software_total'])) {
			$this->data['web_payment_software_total'] = $_POST['web_payment_software_total'];
		} else {
			$this->data['web_payment_software_total'] = $this->config->get('web_payment_software_total');
		}

		if (isset($_POST['web_payment_software_sort_order'])) {
			$this->data['web_payment_software_sort_order'] = $_POST['web_payment_software_sort_order'];
		} else {
			$this->data['web_payment_software_sort_order'] = $this->config->get('web_payment_software_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/web_payment_software')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['web_payment_software_merchant_name']) {
			$this->error['login'] = $this->_('error_login');
		}

		if (!$_POST['web_payment_software_merchant_key']) {
			$this->error['key'] = $this->_('error_key');
		}

		return $this->error ? false : true;
	}
}
