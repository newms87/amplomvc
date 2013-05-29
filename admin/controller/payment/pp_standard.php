<?php
class ControllerPaymentPPStandard extends Controller 
{
	

	public function index()
	{
		$this->template->load('payment/pp_standard');

		$this->load->language('payment/pp_standard');

		$this->document->setTitle($this->_('heading_title'));

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('pp_standard', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/payment'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('payment/pp_standard'));
		
		$this->data['action'] = $this->url->link('payment/pp_standard');

		$this->data['cancel'] = $this->url->link('extension/payment');

		$defaults = array('pp_standard_email'=>$this->config->get('pp_standard_email'),
				'pp_standard_test'=>$this->config->get('pp_standard_test'),
				'pp_standard_transaction'=>$this->config->get('pp_standard_transaction'),
				'pp_standard_debug'=>$this->config->get('pp_standard_debug'),
				'pp_standard_total'=>$this->config->get('pp_standard_total'),
				'pp_standard_canceled_reversal_status_id'=>$this->config->get('pp_standard_canceled_reversal_status_id'),
				'pp_standard_completed_status_id'=>$this->config->get('pp_standard_completed_status_id'),
				'pp_standard_denied_status_id'=>$this->config->get('pp_standard_denied_status_id'),
				'pp_standard_expired_status_id'=>$this->config->get('pp_standard_expired_status_id'),
				'pp_standard_failed_status_id'=>$this->config->get('pp_standard_failed_status_id'),
				'pp_standard_pending_status_id'=>$this->config->get('pp_standard_pending_status_id'),
				'pp_standard_processed_status_id'=>$this->config->get('pp_standard_processed_status_id'),
				'pp_standard_refunded_status_id'=>$this->config->get('pp_standard_refunded_status_id'),
				'pp_standard_reversed_status_id'=>$this->config->get('pp_standard_reversed_status_id'),
				'pp_standard_voided_status_id'=>$this->config->get('pp_standard_voided_status_id'),
				'pp_standard_geo_zone_id'=>$this->config->get('pp_standard_geo_zone_id'),
				'pp_standard_status'=>$this->config->get('pp_standard_status'),
				'pp_standard_sort_order'=>$this->config->get('pp_standard_sort_order'),
				'pp_standard_page_style'=>$this->config->get('pp_standard_page_style')
			);
		
		foreach ($defaults as $key=>$default) {
			$this->data[$key] = isset($_GET[$key])?$_GET[$key]:$default;
		}
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

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
