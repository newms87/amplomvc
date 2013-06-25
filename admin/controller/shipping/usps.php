<?php
class Admin_Controller_Shipping_Usps extends Controller 
{
	

	public function index()
	{
		$this->template->load('shipping/usps');

		$this->load->language('shipping/usps');

		$this->document->setTitle($this->_('heading_title'));

		if ($this->request->isPost() && $this->validate()) {
			$this->Model_Setting_Setting->editSetting('usps', $_POST);

			$this->message->add('success', $this->_('text_success'));

			$this->url->redirect($this->url->link('extension/shipping'));
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['user_id'])) {
			$this->data['error_user_id'] = $this->error['user_id'];
		} else {
			$this->data['error_user_id'] = '';
		}

 		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}

		if (isset($this->error['width'])) {
			$this->data['error_width'] = $this->error['width'];
		} else {
			$this->data['error_width'] = '';
		}

		if (isset($this->error['length'])) {
			$this->data['error_length'] = $this->error['length'];
		} else {
			$this->data['error_length'] = '';
		}

		if (isset($this->error['height'])) {
			$this->data['error_height'] = $this->error['height'];
		} else {
			$this->data['error_height'] = '';
		}

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('text_shipping'), $this->url->link('extension/shipping'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('shipping/usps'));

		$this->data['action'] = $this->url->link('shipping/usps');

		$this->data['cancel'] = $this->url->link('extension/shipping');

		if (isset($_POST['usps_user_id'])) {
			$this->data['usps_user_id'] = $_POST['usps_user_id'];
		} else {
			$this->data['usps_user_id'] = $this->config->get('usps_user_id');
		}

		if (isset($_POST['usps_postcode'])) {
			$this->data['usps_postcode'] = $_POST['usps_postcode'];
		} else {
			$this->data['usps_postcode'] = $this->config->get('usps_postcode');
		}

		if (isset($_POST['usps_domestic_00'])) {
			$this->data['usps_domestic_00'] = $_POST['usps_domestic_00'];
		} else {
			$this->data['usps_domestic_00'] = $this->config->get('usps_domestic_00');
		}

		if (isset($_POST['usps_domestic_01'])) {
			$this->data['usps_domestic_01'] = $_POST['usps_domestic_01'];
		} else {
			$this->data['usps_domestic_01'] = $this->config->get('usps_domestic_01');
		}

		if (isset($_POST['usps_domestic_02'])) {
			$this->data['usps_domestic_02'] = $_POST['usps_domestic_02'];
		} else {
			$this->data['usps_domestic_02'] = $this->config->get('usps_domestic_02');
		}

		if (isset($_POST['usps_domestic_03'])) {
			$this->data['usps_domestic_03'] = $_POST['usps_domestic_03'];
		} else {
			$this->data['usps_domestic_03'] = $this->config->get('usps_domestic_03');
		}

		if (isset($_POST['usps_domestic_1'])) {
			$this->data['usps_domestic_1'] = $_POST['usps_domestic_1'];
		} else {
			$this->data['usps_domestic_1'] = $this->config->get('usps_domestic_1');
		}

		if (isset($_POST['usps_domestic_2'])) {
			$this->data['usps_domestic_2'] = $_POST['usps_domestic_2'];
		} else {
			$this->data['usps_domestic_2'] = $this->config->get('usps_domestic_2');
		}

		if (isset($_POST['usps_domestic_3'])) {
			$this->data['usps_domestic_3'] = $_POST['usps_domestic_3'];
		} else {
			$this->data['usps_domestic_3'] = $this->config->get('usps_domestic_3');
		}

		if (isset($_POST['usps_domestic_4'])) {
			$this->data['usps_domestic_4'] = $_POST['usps_domestic_4'];
		} else {
			$this->data['usps_domestic_4'] = $this->config->get('usps_domestic_4');
		}

		if (isset($_POST['usps_domestic_5'])) {
			$this->data['usps_domestic_5'] = $_POST['usps_domestic_5'];
		} else {
			$this->data['usps_domestic_5'] = $this->config->get('usps_domestic_5');
		}

		if (isset($_POST['usps_domestic_6'])) {
			$this->data['usps_domestic_6'] = $_POST['usps_domestic_6'];
		} else {
			$this->data['usps_domestic_6'] = $this->config->get('usps_domestic_6');
		}

		if (isset($_POST['usps_domestic_7'])) {
			$this->data['usps_domestic_7'] = $_POST['usps_domestic_7'];
		} else {
			$this->data['usps_domestic_7'] = $this->config->get('usps_domestic_7');
		}

		if (isset($_POST['usps_domestic_12'])) {
			$this->data['usps_domestic_12'] = $_POST['usps_domestic_12'];
		} else {
			$this->data['usps_domestic_12'] = $this->config->get('usps_domestic_12');
		}

		if (isset($_POST['usps_domestic_13'])) {
			$this->data['usps_domestic_13'] = $_POST['usps_domestic_13'];
		} else {
			$this->data['usps_domestic_13'] = $this->config->get('usps_domestic_13');
		}

		if (isset($_POST['usps_domestic_16'])) {
			$this->data['usps_domestic_16'] = $_POST['usps_domestic_16'];
		} else {
			$this->data['usps_domestic_16'] = $this->config->get('usps_domestic_16');
		}

		if (isset($_POST['usps_domestic_17'])) {
			$this->data['usps_domestic_17'] = $_POST['usps_domestic_17'];
		} else {
			$this->data['usps_domestic_17'] = $this->config->get('usps_domestic_17');
		}

		if (isset($_POST['usps_domestic_18'])) {
			$this->data['usps_domestic_18'] = $_POST['usps_domestic_18'];
		} else {
			$this->data['usps_domestic_18'] = $this->config->get('usps_domestic_18');
		}

		if (isset($_POST['usps_domestic_19'])) {
			$this->data['usps_domestic_19'] = $_POST['usps_domestic_19'];
		} else {
			$this->data['usps_domestic_19'] = $this->config->get('usps_domestic_19');
		}

		if (isset($_POST['usps_domestic_22'])) {
			$this->data['usps_domestic_22'] = $_POST['usps_domestic_22'];
		} else {
			$this->data['usps_domestic_22'] = $this->config->get('usps_domestic_22');
		}

		if (isset($_POST['usps_domestic_23'])) {
			$this->data['usps_domestic_23'] = $_POST['usps_domestic_23'];
		} else {
			$this->data['usps_domestic_23'] = $this->config->get('usps_domestic_23');
		}

		if (isset($_POST['usps_domestic_25'])) {
			$this->data['usps_domestic_25'] = $_POST['usps_domestic_25'];
		} else {
			$this->data['usps_domestic_25'] = $this->config->get('usps_domestic_25');
		}

		if (isset($_POST['usps_domestic_27'])) {
			$this->data['usps_domestic_27'] = $_POST['usps_domestic_27'];
		} else {
			$this->data['usps_domestic_27'] = $this->config->get('usps_domestic_27');
		}

		if (isset($_POST['usps_domestic_28'])) {
			$this->data['usps_domestic_28'] = $_POST['usps_domestic_28'];
		} else {
			$this->data['usps_domestic_28'] = $this->config->get('usps_domestic_28');
		}

		if (isset($_POST['usps_international_1'])) {
			$this->data['usps_international_1'] = $_POST['usps_international_1'];
		} else {
			$this->data['usps_international_1'] = $this->config->get('usps_international_1');
		}

		if (isset($_POST['usps_international_2'])) {
			$this->data['usps_international_2'] = $_POST['usps_international_2'];
		} else {
			$this->data['usps_international_2'] = $this->config->get('usps_international_2');
		}

		if (isset($_POST['usps_international_4'])) {
			$this->data['usps_international_4'] = $_POST['usps_international_4'];
		} else {
			$this->data['usps_international_4'] = $this->config->get('usps_international_4');
		}

		if (isset($_POST['usps_international_5'])) {
			$this->data['usps_international_5'] = $_POST['usps_international_5'];
		} else {
			$this->data['usps_international_5'] = $this->config->get('usps_international_5');
		}

		if (isset($_POST['usps_international_6'])) {
			$this->data['usps_international_6'] = $_POST['usps_international_6'];
		} else {
			$this->data['usps_international_6'] = $this->config->get('usps_international_6');
		}

		if (isset($_POST['usps_international_7'])) {
			$this->data['usps_international_7'] = $_POST['usps_international_7'];
		} else {
			$this->data['usps_international_7'] = $this->config->get('usps_international_7');
		}

		if (isset($_POST['usps_international_8'])) {
			$this->data['usps_international_8'] = $_POST['usps_international_8'];
		} else {
			$this->data['usps_international_8'] = $this->config->get('usps_international_8');
		}

		if (isset($_POST['usps_international_9'])) {
			$this->data['usps_international_9'] = $_POST['usps_international_9'];
		} else {
			$this->data['usps_international_9'] = $this->config->get('usps_international_9');
		}

		if (isset($_POST['usps_international_10'])) {
			$this->data['usps_international_10'] = $_POST['usps_international_10'];
		} else {
			$this->data['usps_international_10'] = $this->config->get('usps_international_10');
		}

		if (isset($_POST['usps_international_11'])) {
			$this->data['usps_international_11'] = $_POST['usps_international_11'];
		} else {
			$this->data['usps_international_11'] = $this->config->get('usps_international_11');
		}

		if (isset($_POST['usps_international_12'])) {
			$this->data['usps_international_12'] = $_POST['usps_international_12'];
		} else {
			$this->data['usps_international_12'] = $this->config->get('usps_international_12');
		}

		if (isset($_POST['usps_international_13'])) {
			$this->data['usps_international_13'] = $_POST['usps_international_13'];
		} else {
			$this->data['usps_international_13'] = $this->config->get('usps_international_13');
		}

		if (isset($_POST['usps_international_14'])) {
			$this->data['usps_international_14'] = $_POST['usps_international_14'];
		} else {
			$this->data['usps_international_14'] = $this->config->get('usps_international_14');
		}

		if (isset($_POST['usps_international_15'])) {
			$this->data['usps_international_15'] = $_POST['usps_international_15'];
		} else {
			$this->data['usps_international_15'] = $this->config->get('usps_international_15');
		}

		if (isset($_POST['usps_international_16'])) {
			$this->data['usps_international_16'] = $_POST['usps_international_16'];
		} else {
			$this->data['usps_international_16'] = $this->config->get('usps_international_16');
		}

		if (isset($_POST['usps_international_21'])) {
			$this->data['usps_international_21'] = $_POST['usps_international_21'];
		} else {
			$this->data['usps_international_21'] = $this->config->get('usps_international_21');
		}

		if (isset($_POST['usps_size'])) {
			$this->data['usps_size'] = $_POST['usps_size'];
		} else {
			$this->data['usps_size'] = $this->config->get('usps_size');
		}

		$this->data['sizes'] = array();

		$this->data['sizes'][] = array(
			'text'  => $this->_('text_regular'),
			'value' => 'REGULAR'
		);

		$this->data['sizes'][] = array(
			'text'  => $this->_('text_large'),
			'value' => 'LARGE'
		);

		if (isset($_POST['usps_container'])) {
			$this->data['usps_container'] = $_POST['usps_container'];
		} else {
			$this->data['usps_container'] = $this->config->get('usps_container');
		}

		$this->data['containers'] = array();

		$this->data['containers'][] = array(
			'text'  => $this->_('text_rectangular'),
			'value' => 'RECTANGULAR'
		);

		$this->data['containers'][] = array(
			'text'  => $this->_('text_non_rectangular'),
			'value' => 'NONRECTANGULAR'
		);

		$this->data['containers'][] = array(
			'text'  => $this->_('text_variable'),
			'value' => 'VARIABLE'
		);

		if (isset($_POST['usps_machinable'])) {
			$this->data['usps_machinable'] = $_POST['usps_machinable'];
		} else {
			$this->data['usps_machinable'] = $this->config->get('usps_machinable');
		}

		if (isset($_POST['usps_length'])) {
			$this->data['usps_length'] = $_POST['usps_length'];
		} else {
			$this->data['usps_length'] = $this->config->get('usps_length');
		}

		if (isset($_POST['usps_width'])) {
			$this->data['usps_width'] = $_POST['usps_width'];
		} else {
			$this->data['usps_width'] = $this->config->get('usps_width');
		}

		if (isset($_POST['usps_height'])) {
			$this->data['usps_height'] = $_POST['usps_height'];
		} else {
			$this->data['usps_height'] = $this->config->get('usps_height');
		}

		if (isset($_POST['usps_length'])) {
			$this->data['usps_length'] = $_POST['usps_length'];
		} else {
			$this->data['usps_length'] = $this->config->get('usps_length');
		}

		if (isset($_POST['usps_display_time'])) {
			$this->data['usps_display_time'] = $_POST['usps_display_time'];
		} else {
			$this->data['usps_display_time'] = $this->config->get('usps_display_time');
		}

		if (isset($_POST['usps_display_weight'])) {
			$this->data['usps_display_weight'] = $_POST['usps_display_weight'];
		} else {
			$this->data['usps_display_weight'] = $this->config->get('usps_display_weight');
		}

		if (isset($_POST['usps_weight_class_id'])) {
			$this->data['usps_weight_class_id'] = $_POST['usps_weight_class_id'];
		} else {
			$this->data['usps_weight_class_id'] = $this->config->get('usps_weight_class_id');
		}

		$this->data['weight_classes'] = $this->Model_Localisation_WeightClass->getWeightClasses();

		if (isset($_POST['usps_tax_class_id'])) {
			$this->data['usps_tax_class_id'] = $_POST['usps_tax_class_id'];
		} else {
			$this->data['usps_tax_class_id'] = $this->config->get('usps_tax_class_id');
		}

		if (isset($_POST['usps_geo_zone_id'])) {
			$this->data['usps_geo_zone_id'] = $_POST['usps_geo_zone_id'];
		} else {
			$this->data['usps_geo_zone_id'] = $this->config->get('usps_geo_zone_id');
		}

		if (isset($_POST['usps_debug'])) {
			$this->data['usps_debug'] = $_POST['usps_debug'];
		} else {
			$this->data['usps_debug'] = $this->config->get('usps_debug');
		}

		if (isset($_POST['usps_status'])) {
			$this->data['usps_status'] = $_POST['usps_status'];
		} else {
			$this->data['usps_status'] = $this->config->get('usps_status');
		}

		if (isset($_POST['usps_sort_order'])) {
			$this->data['usps_sort_order'] = $_POST['usps_sort_order'];
		} else {
			$this->data['usps_sort_order'] = $this->config->get('usps_sort_order');
		}

		$this->data['tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();

		$this->data['geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->data['breadcrumbs'] = $this->breadcrumb->render();
		
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'shipping/usps')) {
			$this->error['warning'] = $this->_('error_permission');
		}

		if (!$_POST['usps_user_id']) {
			$this->error['user_id'] = $this->_('error_user_id');
		}

		if (!$_POST['usps_postcode']) {
			$this->error['postcode'] = $this->_('error_postcode');
		}

		if (!$_POST['usps_width']) {
			$this->error['width'] = $this->_('error_width');
		}

		if (!$_POST['usps_height']) {
			$this->error['height'] = $this->_('error_height');
		}

		if (!$_POST['usps_length']) {
			$this->error['length'] = $this->_('error_length');
		}

		return $this->error ? false : true;
	}
}