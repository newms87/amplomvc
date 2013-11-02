<?php
class Admin_Controller_Shipping_Amount extends Controller
{

	public function index()
	{
		$this->template->load('shipping/amount');

		$this->language->load('shipping/amount');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('amount', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/shipping');
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('shipping/amount'));

		$this->data['action'] = $this->url->link('shipping/amount');

		$this->data['cancel'] = $this->url->link('extension/shipping');

		$config_values = array(
			'amount_priceset',
			'amount_zonerule',
			'amount_tax_class_id',
			'amount_geo_zone_id',
			'amount_status',
			'amount_sort_order'
		);
		foreach ($config_values as $cv) {
			$this->data[$cv] = isset($_POST[$cv]) ? $_POST[$cv] : $this->config->get($cv);
		}

		if (!is_array($this->data['amount_priceset'])) {
			$this->data['amount_priceset'] = array();
		}

		if (!is_array($this->data['amount_zonerule'])) {
			$this->data['amount_zonerule'] = array();
		}

		$this->data['tax_classes'] = array_merge(array(0 => '--- None ---'), $this->Model_Localisation_TaxClass->getTaxClasses());

		$this->data['geo_zones'] = array_merge(array(0 => '--- None ---'), $this->Model_Localisation_GeoZone->getGeoZones());

		//set default to USA
		$this->data['default_country'] = 223;
		$this->data['countries']       = $this->Model_Localisation_Country->getCountries();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'shipping/amount')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
