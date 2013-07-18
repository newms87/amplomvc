<?php
class Catalog_Model_Payment_Cod extends Model 
{
  	public function getMethod($address, $total)
  	{
		$this->language->load('payment/cod');
		
		if ((int)$this->config->get('cod_total') > $total) {
			return array();
		}
		
		if (!$this->address->inGeoZone($address, $this->config->get('cod_geo_zone_id'))) {
			return array();
		}
		
		$method_data = array(
			'code'		=> 'cod',
			'title'		=> $this->_('text_title'),
			'sort_order' => $this->config->get('cod_sort_order')
		);

		return $method_data;
  	}
}