<?php
class Admin_Controller_Payment_BankTransfer extends Controller
{


	public function index()
	{
		$this->template->load('payment/bank_transfer');

		$this->language->load('payment/bank_transfer');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('bank_transfer', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/payment');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$languages = $this->Model_Localisation_Language->getLanguages();

		foreach ($languages as $language) {
			if (isset($this->error['bank_' . $language['language_id']])) {
				$this->data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
			} else {
				$this->data['error_bank_' . $language['language_id']] = '';
			}
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_payment'), $this->url->link('extension/payment'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('payment/bank_transfer'));

		$this->data['action'] = $this->url->link('payment/bank_transfer');

		$this->data['cancel'] = $this->url->link('extension/payment');

		foreach ($languages as $language) {
			if (isset($_POST['bank_transfer_bank_' . $language['language_id']])) {
				$this->data['bank_transfer_bank_' . $language['language_id']] = $_POST['bank_transfer_bank_' . $language['language_id']];
			} else {
				$this->data['bank_transfer_bank_' . $language['language_id']] = $this->config->get('bank_transfer_bank_' . $language['language_id']);
			}
		}

		$this->data['languages'] = $languages;

		if (isset($_POST['bank_transfer_total'])) {
			$this->data['bank_transfer_total'] = $_POST['bank_transfer_total'];
		} else {
			$this->data['bank_transfer_total'] = $this->config->get('bank_transfer_total');
		}

		if (isset($_POST['bank_transfer_order_status_id'])) {
			$this->data['bank_transfer_order_status_id'] = $_POST['bank_transfer_order_status_id'];
		} else {
			$this->data['bank_transfer_order_status_id'] = $this->config->get('bank_transfer_order_status_id');
		}

		$this->data['order_statuses'] = $this->order->getOrderStatuses();

		if (isset($_POST['bank_transfer_geo_zone_id'])) {
			$this->data['bank_transfer_geo_zone_id'] = $_POST['bank_transfer_geo_zone_id'];
		} else {
			$this->data['bank_transfer_geo_zone_id'] = $this->config->get('bank_transfer_geo_zone_id');
		}

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		if (isset($_POST['bank_transfer_status'])) {
			$this->data['bank_transfer_status'] = $_POST['bank_transfer_status'];
		} else {
			$this->data['bank_transfer_status'] = $this->config->get('bank_transfer_status');
		}

		if (isset($_POST['bank_transfer_sort_order'])) {
			$this->data['bank_transfer_sort_order'] = $_POST['bank_transfer_sort_order'];
		} else {
			$this->data['bank_transfer_sort_order'] = $this->config->get('bank_transfer_sort_order');
		}


		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/bank_transfer')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		$languages = $this->Model_Localisation_Language->getLanguages();

		foreach ($languages as $language) {
			if (!$_POST['bank_transfer_bank_' . $language['language_id']]) {
				$this->error['bank_' . $language['language_id']] = $this->_('error_bank');
			}
		}

		return $this->error ? false : true;
	}
}
