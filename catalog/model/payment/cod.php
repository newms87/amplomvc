<?php
class Catalog_Model_Payment_Cod extends Model
{
	function __construct($registry)
	{
		parent::__construct($registry);

		$this->language->load('payment/cod');
	}

	public function getMethod($address, $total)
	{
		if ((int)$this->config->get('cod_total') > $total) {
			return array();
		}

		if (!$this->address->inGeoZone($address, $this->config->get('cod_geo_zone_id'))) {
			return array();
		}

		return $this->data();
	}

	public function data()
	{
		$method_data = array(
			'code'       => 'cod',
			'title'      => $this->_('text_cod_title'),
			'sort_order' => $this->config->get('cod_sort_order')
		);

		return $method_data;
	}
}
