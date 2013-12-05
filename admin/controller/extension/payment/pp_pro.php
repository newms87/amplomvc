<?php
class Admin_Controller_Payment_PpPro extends Controller
{


	public function index()
	{
		$this->template->load('payment/pp_pro');

		$this->language->load('payment/pp_pro');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('pp_pro', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/payment');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

		if (isset($this->error['signature'])) {
			$this->data['error_signature'] = $this->error['signature'];
		} else {
			$this->data['error_signature'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/pp_pro'));

		$this->data['action'] = $this->url->link('payment/pp_pro');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['pp_pro_username'])) {
			$this->data['pp_pro_username'] = $_POST['pp_pro_username'];
		} else {
			$this->data['pp_pro_username'] = $this->config->get('pp_pro_username');
		}

		if (isset($_POST['pp_pro_password'])) {
			$this->data['pp_pro_password'] = $_POST['pp_pro_password'];
		} else {
			$this->data['pp_pro_password'] = $this->config->get('pp_pro_password');
		}

		if (isset($_POST['pp_pro_signature'])) {
			$this->data['pp_pro_signature'] = $_POST['pp_pro_signature'];
		} else {
			$this->data['pp_pro_signature'] = $this->config->get('pp_pro_signature');
		}

		if (isset($_POST['pp_pro_test'])) {
			$this->data['pp_pro_test'] = $_POST['pp_pro_test'];
		} else {
			$this->data['pp_pro_test'] = $this->config->get('pp_pro_test');
		}

		if (isset($_POST['pp_pro_method'])) {
			$this->data['pp_pro_transaction'] = $_POST['pp_pro_transaction'];
		} else {
			$this->data['pp_pro_transaction'] = $this->config->get('pp_pro_transaction');
		}

		if (isset($_POST['pp_pro_total'])) {
			$this->data['pp_pro_total'] = $_POST['pp_pro_total'];
		} else {
			$this->data['pp_pro_total'] = $this->config->get('pp_pro_total');
		}

		if (isset($_POST['pp_pro_order_status_id'])) {
			$this->data['pp_pro_order_status_id'] = $_POST['pp_pro_order_status_id'];
		} else {
			$this->data['pp_pro_order_status_id'] = $this->config->get('pp_pro_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['pp_pro_geo_zone_id'])) {
			$this->data['pp_pro_geo_zone_id'] = $_POST['pp_pro_geo_zone_id'];
		} else {
			$this->data['pp_pro_geo_zone_id'] = $this->config->get('pp_pro_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['pp_pro_status'])) {
			$this->data['pp_pro_status'] = $_POST['pp_pro_status'];
		} else {
			$this->data['pp_pro_status'] = $this->config->get('pp_pro_status');
		}

		if (isset($_POST['pp_pro_sort_order'])) {
			$this->data['pp_pro_sort_order'] = $_POST['pp_pro_sort_order'];
		} else {
			$this->data['pp_pro_sort_order'] = $this->config->get('pp_pro_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'payment/pp_pro')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['pp_pro_username']) {
			$this->error['username'] = $this->_('error_username');
		}

		if (!$_POST['pp_pro_password']) {
			$this->error['password'] = $this->_('error_password');
		}

		if (!$_POST['pp_pro_signature']) {
			$this->error['signature'] = $this->_('error_signature');
		}

		return $this->error ? false : true;
	}
}
