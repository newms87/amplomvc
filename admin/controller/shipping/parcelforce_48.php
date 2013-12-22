<?php
class Admin_Controller_Shipping_Parcelforce4848_Controller_Shipping_Parcelforce4848 extends Controller
{


	public function index()
	{
		$this->template->load('shipping/parcelforce_48');

		$this->language->load('shipping/parcelforce_48');

		$this->document->setTitle($this->_('head_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('parcelforce_48', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect('extension/shipping');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
		$this->breadcrumb->add($this->_('head_title'), $this->url->link('shipping/parcelforce_48'));

		$this->data['action'] = $this->url->link('shipping/parcelforce_48');

		$this->data['cancel'] = $this->url->link('extension/shipping');

		if (isset($_POST['parcelforce_48_rate'])) {
			$this->data['parcelforce_48_rate'] = $_POST['parcelforce_48_rate'];
		} elseif ($this->config->get('parcelforce_48_rate')) {
			$this->data['parcelforce_48_rate'] = $this->config->get('parcelforce_48_rate');
		} else {
			$this->data['parcelforce_48_rate'] = '10:15.99,12:19.99,14:20.99,16:21.99,18:21.99,20:21.99,22:26.99,24:30.99,26:34.99,28:38.99,30:42.99,35:52.99,40:62.99,45:72.99,50:82.99,55:92.99,60:102.99,65:112.99,70:122.99,75:132.99,80:142.99,85:152.99,90:162.99,95:172.99,100:182.99';
		}

		if (isset($_POST['parcelforce_48_insurance'])) {
			$this->data['parcelforce_48_insurance'] = $_POST['parcelforce_48_insurance'];
		} elseif ($this->config->get('parcelforce_48_insurance')) {
			$this->data['parcelforce_48_insurance'] = $this->config->get('parcelforce_48_insurance');
		} else {
			$this->data['parcelforce_48_insurance'] = '150:0,500:12,1000:24,1500:36,2000:48,2500:60';
		}

		if (isset($_POST['parcelforce_48_display_weight'])) {
			$this->data['parcelforce_48_display_weight'] = $_POST['parcelforce_48_display_weight'];
		} else {
			$this->data['parcelforce_48_display_weight'] = $this->config->get('parcelforce_48_display_weight');
		}

		if (isset($_POST['parcelforce_48_display_insurance'])) {
			$this->data['parcelforce_48_display_insurance'] = $_POST['parcelforce_48_display_insurance'];
		} else {
			$this->data['parcelforce_48_display_insurance'] = $this->config->get('parcelforce_48_display_insurance');
		}

		if (isset($_POST['parcelforce_48_display_time'])) {
			$this->data['parcelforce_48_display_time'] = $_POST['parcelforce_48_display_time'];
		} else {
			$this->data['parcelforce_48_display_time'] = $this->config->get('parcelforce_48_display_time');
		}

		if (isset($_POST['parcelforce_48_tax_class_id'])) {
			$this->data['parcelforce_48_tax_class_id'] = $_POST['parcelforce_48_tax_class_id'];
		} else {
			$this->data['parcelforce_48_tax_class_id'] = $this->config->get('parcelforce_48_tax_class_id');
		}

		if (isset($_POST['parcelforce_48_geo_zone_id'])) {
			$this->data['parcelforce_48_geo_zone_id'] = $_POST['parcelforce_48_geo_zone_id'];
		} else {
			$this->data['parcelforce_48_geo_zone_id'] = $this->config->get('parcelforce_48_geo_zone_id');
		}

		if (isset($_POST['parcelforce_48_status'])) {
			$this->data['parcelforce_48_status'] = $_POST['parcelforce_48_status'];
		} else {
			$this->data['parcelforce_48_status'] = $this->config->get('parcelforce_48_status');
		}

		if (isset($_POST['parcelforce_48_sort_order'])) {
			$this->data['parcelforce_48_sort_order'] = $_POST['parcelforce_48_sort_order'];
		} else {
			$this->data['parcelforce_48_sort_order'] = $this->config->get('parcelforce_48_sort_order');
		}

		$this->data['tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'shipping/parcelforce_48')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		return $this->error ? false : true;
	}
}
