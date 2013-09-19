<?php
class Admin_Controller_Shipping_Weight extends Controller
{


	public function index()
	{
		$this->template->load('shipping/weight');

		$this->language->load('shipping/weight');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->System_Model_Setting->editSetting('weight', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/shipping'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('shipping/weight'));

		$this->data['action'] = $this->url->link('shipping/weight');

		$this->data['cancel'] = $this->url->link('extension/shipping');

		$geo_zones = $this->Model_Localisation_GeoZone->getGeoZones();

		foreach ($geo_zones as $geo_zone) {
			if (isset($_POST['weight_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $_POST['weight_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_rate');
			}

			if (isset($_POST['weight_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $_POST['weight_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$this->data['weight_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('weight_' . $geo_zone['geo_zone_id'] . '_status');
			}
		}

		$this->data['geo_zones'] = $geo_zones;

		if (isset($_POST['weight_tax_class_id'])) {
			$this->data['weight_tax_class_id'] = $_POST['weight_tax_class_id'];
		} else {
			$this->data['weight_tax_class_id'] = $this->config->get('weight_tax_class_id');
		}

		if (isset($_POST['weight_status'])) {
			$this->data['weight_status'] = $_POST['weight_status'];
		} else {
			$this->data['weight_status'] = $this->config->get('weight_status');
		}

		if (isset($_POST['weight_sort_order'])) {
			$this->data['weight_sort_order'] = $_POST['weight_sort_order'];
		} else {
			$this->data['weight_sort_order'] = $this->config->get('weight_sort_order');
		}

		$this->data['tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'shipping/weight')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
