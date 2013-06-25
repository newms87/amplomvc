<?php
class Admin_Controller_Payment_PpStandard extends Controller 
{
	public function index()
	{
		$this->template->load('payment/pp_standard');
		$this->load->language('payment/pp_standard');

		$this->document->setTitle($this->_('heading_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('pp_standard', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/pp_standard'));
		
		$this->data['action'] = $this->url->link('payment/pp_standard');

		$this->data['cancel'] = $this->url->link('extension/payment');

		$defaults = array(
			'pp_standard_email' => '',
			'pp_standard_test' => '',
			'pp_standard_transaction' => '',
			'pp_standard_debug' => '',
			'pp_standard_total' => '',
			'pp_standard_canceled_reversal_status_id' => '',
			'pp_standard_completed_status_id' => '',
			'pp_standard_denied_status_id' => '',
			'pp_standard_expired_status_id' => '',
			'pp_standard_failed_status_id' => '',
			'pp_standard_pending_status_id' => '',
			'pp_standard_processed_status_id' => '',
			'pp_standard_refunded_status_id' => '',
			'pp_standard_reversed_status_id' => '',
			'pp_standard_voided_status_id' => '',
			'pp_standard_geo_zone_id' => '',
			'pp_standard_status' => '',
			'pp_standard_sort_order' => '',
			'pp_standard_page_style' => '',
			'pp_standard_pdt_enabled' => false,
			'pp_standard_auto_return_url' => '',
		);
		
		foreach ($defaults as $key => $default) {
			if (isset($_GET[$key])) {
				$this->data[$key] = $_GET[$key];
			} elseif(!is_null($this->config->get($key))) {
				$this->data[$key] = $this->config->get($key);
			} else {
				$this->data[$key] = $default;
			}
		}
		
		$this->data['data_order_statuses'] = $this->Model_Localisation_OrderStatus->getOrderStatuses();
		
		$this->data['data_geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/pp_standard')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['pp_standard_email']) {
			$this->error['pp_standard_email'] = $this->_('error_email');
		}

		return $this->error ? false : true;
	}
}
