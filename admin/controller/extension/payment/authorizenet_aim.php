<?php
class Admin_Controller_Payment_AuthorizenetAim extends Controller
{


	public function index()
	{
		$this->template->load('payment/authorizenet_aim');

		$this->language->load('payment/authorizenet_aim');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('authorizenet_aim', $_POST);

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
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/authorizenet_aim'));

		$this->data['action'] = $this->url->link('payment/authorizenet_aim');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['authorizenet_aim_login'])) {
			$this->data['authorizenet_aim_login'] = $_POST['authorizenet_aim_login'];
		} else {
			$this->data['authorizenet_aim_login'] = $this->config->get('authorizenet_aim_login');
		}

		if (isset($_POST['authorizenet_aim_key'])) {
			$this->data['authorizenet_aim_key'] = $_POST['authorizenet_aim_key'];
		} else {
			$this->data['authorizenet_aim_key'] = $this->config->get('authorizenet_aim_key');
		}

		if (isset($_POST['authorizenet_aim_hash'])) {
			$this->data['authorizenet_aim_hash'] = $_POST['authorizenet_aim_hash'];
		} else {
			$this->data['authorizenet_aim_hash'] = $this->config->get('authorizenet_aim_hash');
		}

		if (isset($_POST['authorizenet_aim_server'])) {
			$this->data['authorizenet_aim_server'] = $_POST['authorizenet_aim_server'];
		} else {
			$this->data['authorizenet_aim_server'] = $this->config->get('authorizenet_aim_server');
		}

		if (isset($_POST['authorizenet_aim_mode'])) {
			$this->data['authorizenet_aim_mode'] = $_POST['authorizenet_aim_mode'];
		} else {
			$this->data['authorizenet_aim_mode'] = $this->config->get('authorizenet_aim_mode');
		}

		if (isset($_POST['authorizenet_aim_method'])) {
			$this->data['authorizenet_aim_method'] = $_POST['authorizenet_aim_method'];
		} else {
			$this->data['authorizenet_aim_method'] = $this->config->get('authorizenet_aim_method');
		}

		if (isset($_POST['authorizenet_aim_total'])) {
			$this->data['authorizenet_aim_total'] = $_POST['authorizenet_aim_total'];
		} else {
			$this->data['authorizenet_aim_total'] = $this->config->get('authorizenet_aim_total');
		}

		if (isset($_POST['authorizenet_aim_order_status_id'])) {
			$this->data['authorizenet_aim_order_status_id'] = $_POST['authorizenet_aim_order_status_id'];
		} else {
			$this->data['authorizenet_aim_order_status_id'] = $this->config->get('authorizenet_aim_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['authorizenet_aim_geo_zone_id'])) {
			$this->data['authorizenet_aim_geo_zone_id'] = $_POST['authorizenet_aim_geo_zone_id'];
		} else {
			$this->data['authorizenet_aim_geo_zone_id'] = $this->config->get('authorizenet_aim_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['authorizenet_aim_status'])) {
			$this->data['authorizenet_aim_status'] = $_POST['authorizenet_aim_status'];
		} else {
			$this->data['authorizenet_aim_status'] = $this->config->get('authorizenet_aim_status');
		}

		if (isset($_POST['authorizenet_aim_sort_order'])) {
			$this->data['authorizenet_aim_sort_order'] = $_POST['authorizenet_aim_sort_order'];
		} else {
			$this->data['authorizenet_aim_sort_order'] = $this->config->get('authorizenet_aim_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'payment/authorizenet_aim')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['authorizenet_aim_login']) {
			$this->error['login'] = $this->_('error_login');
		}

		if (!$_POST['authorizenet_aim_key']) {
			$this->error['key'] = $this->_('error_key');
		}

		return $this->error ? false : true;
	}
}
