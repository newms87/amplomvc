<?php
class Admin_Controller_Payment_Nochex extends Controller
{


	public function index()
	{
		$this->template->load('payment/nochex');

		$this->language->load('payment/nochex');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('nochex', $_POST);

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

		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/nochex'));

		$this->data['action'] = $this->url->link('payment/nochex');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['nochex_email'])) {
			$this->data['nochex_email'] = $_POST['nochex_email'];
		} else {
			$this->data['nochex_email'] = $this->config->get('nochex_email');
		}

		if (isset($_POST['nochex_account'])) {
			$this->data['nochex_account'] = $_POST['nochex_account'];
		} else {
			$this->data['nochex_account'] = $this->config->get('nochex_account');
		}

		if (isset($_POST['nochex_merchant'])) {
			$this->data['nochex_merchant'] = $_POST['nochex_merchant'];
		} else {
			$this->data['nochex_merchant'] = $this->config->get('nochex_merchant');
		}

		if (isset($_POST['nochex_template'])) {
			$this->data['nochex_template'] = $_POST['nochex_template'];
		} else {
			$this->data['nochex_template'] = $this->config->get('nochex_template');
		}

		if (isset($_POST['nochex_test'])) {
			$this->data['nochex_test'] = $_POST['nochex_test'];
		} else {
			$this->data['nochex_test'] = $this->config->get('nochex_test');
		}

		if (isset($_POST['nochex_total'])) {
			$this->data['nochex_total'] = $_POST['nochex_total'];
		} else {
			$this->data['nochex_total'] = $this->config->get('nochex_total');
		}

		if (isset($_POST['nochex_order_status_id'])) {
			$this->data['nochex_order_status_id'] = $_POST['nochex_order_status_id'];
		} else {
			$this->data['nochex_order_status_id'] = $this->config->get('nochex_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['nochex_geo_zone_id'])) {
			$this->data['nochex_geo_zone_id'] = $_POST['nochex_geo_zone_id'];
		} else {
			$this->data['nochex_geo_zone_id'] = $this->config->get('nochex_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['nochex_status'])) {
			$this->data['nochex_status'] = $_POST['nochex_status'];
		} else {
			$this->data['nochex_status'] = $this->config->get('nochex_status');
		}

		if (isset($_POST['nochex_sort_order'])) {
			$this->data['nochex_sort_order'] = $_POST['nochex_sort_order'];
		} else {
			$this->data['nochex_sort_order'] = $this->config->get('nochex_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/nochex')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['nochex_email']) {
			$this->error['email'] = $this->_('error_email');
		}

		if (!$_POST['nochex_merchant']) {
			$this->error['merchant'] = $this->_('error_merchant');
		}

		return $this->error ? false : true;
	}
}
