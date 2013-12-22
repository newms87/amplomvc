<?php
class Admin_Controller_Payment_Sagepay extends Controller
{


	public function index()
	{
		$this->template->load('payment/sagepay');

		$this->language->load('payment/sagepay');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('sagepay', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/payment');
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

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/sagepay'));

		$this->data['action'] = $this->url->link('payment/sagepay');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['sagepay_vendor'])) {
			$this->data['sagepay_vendor'] = $_POST['sagepay_vendor'];
		} else {
			$this->data['sagepay_vendor'] = $this->config->get('sagepay_vendor');
		}

		if (isset($_POST['sagepay_password'])) {
			$this->data['sagepay_password'] = $_POST['sagepay_password'];
		} else {
			$this->data['sagepay_password'] = $this->config->get('sagepay_password');
		}

		if (isset($_POST['sagepay_test'])) {
			$this->data['sagepay_test'] = $_POST['sagepay_test'];
		} else {
			$this->data['sagepay_test'] = $this->config->get('sagepay_test');
		}

		if (isset($_POST['sagepay_transaction'])) {
			$this->data['sagepay_transaction'] = $_POST['sagepay_transaction'];
		} else {
			$this->data['sagepay_transaction'] = $this->config->get('sagepay_transaction');
		}

		if (isset($_POST['sagepay_total'])) {
			$this->data['sagepay_total'] = $_POST['sagepay_total'];
		} else {
			$this->data['sagepay_total'] = $this->config->get('sagepay_total');
		}

		if (isset($_POST['sagepay_order_status_id'])) {
			$this->data['sagepay_order_status_id'] = $_POST['sagepay_order_status_id'];
		} else {
			$this->data['sagepay_order_status_id'] = $this->config->get('sagepay_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['sagepay_geo_zone_id'])) {
			$this->data['sagepay_geo_zone_id'] = $_POST['sagepay_geo_zone_id'];
		} else {
			$this->data['sagepay_geo_zone_id'] = $this->config->get('sagepay_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['sagepay_status'])) {
			$this->data['sagepay_status'] = $_POST['sagepay_status'];
		} else {
			$this->data['sagepay_status'] = $this->config->get('sagepay_status');
		}

		if (isset($_POST['sagepay_sort_order'])) {
			$this->data['sagepay_sort_order'] = $_POST['sagepay_sort_order'];
		} else {
			$this->data['sagepay_sort_order'] = $this->config->get('sagepay_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'payment/sagepay')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['sagepay_vendor']) {
			$this->error['vendor'] = $this->_('error_vendor');
		}

		if (!$_POST['sagepay_password']) {
			$this->error['password'] = $this->_('error_password');
		}

		return $this->error ? false : true;
	}
}
