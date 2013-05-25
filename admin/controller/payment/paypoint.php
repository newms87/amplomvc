<?php
class ControllerPaymentPayPoint extends Controller {
	

	public function index() {
		$this->template->load('payment/paypoint');

		$this->load->language('payment/paypoint');

		$this->document->setTitle($this->_('heading_title'));

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paypoint', $_POST);

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

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/paypoint'));

		$this->data['action'] = $this->url->link('payment/paypoint');

		$this->data['cancel'] = $this->url->link('extension/payment');

		if (isset($_POST['paypoint_merchant'])) {
			$this->data['paypoint_merchant'] = $_POST['paypoint_merchant'];
		} else {
			$this->data['paypoint_merchant'] = $this->config->get('paypoint_merchant');
		}

		if (isset($_POST['paypoint_password'])) {
			$this->data['paypoint_password'] = $_POST['paypoint_password'];
		} else {
			$this->data['paypoint_password'] = $this->config->get('paypoint_password');
		}

		if (isset($_POST['paypoint_test'])) {
			$this->data['paypoint_test'] = $_POST['paypoint_test'];
		} else {
			$this->data['paypoint_test'] = $this->config->get('paypoint_test');
		}

		if (isset($_POST['paypoint_total'])) {
			$this->data['paypoint_total'] = $_POST['paypoint_total'];
		} else {
			$this->data['paypoint_total'] = $this->config->get('paypoint_total');
		}

		if (isset($_POST['paypoint_order_status_id'])) {
			$this->data['paypoint_order_status_id'] = $_POST['paypoint_order_status_id'];
		} else {
			$this->data['paypoint_order_status_id'] = $this->config->get('paypoint_order_status_id');
		}

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($_POST['paypoint_geo_zone_id'])) {
			$this->data['paypoint_geo_zone_id'] = $_POST['paypoint_geo_zone_id'];
		} else {
			$this->data['paypoint_geo_zone_id'] = $this->config->get('paypoint_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($_POST['paypoint_status'])) {
			$this->data['paypoint_status'] = $_POST['paypoint_status'];
		} else {
			$this->data['paypoint_status'] = $this->config->get('paypoint_status');
		}

		if (isset($_POST['paypoint_sort_order'])) {
			$this->data['paypoint_sort_order'] = $_POST['paypoint_sort_order'];
		} else {
			$this->data['paypoint_sort_order'] = $this->config->get('paypoint_sort_order');
		}

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paypoint')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['paypoint_merchant']) {
			$this->error['merchant'] = $this->_('error_merchant');
		}

		return $this->error ? false : true;
	}
}