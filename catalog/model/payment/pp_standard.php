<?php
class Catalog_Model_Payment_PpStandard extends Model 
{
  	public function getMethod($address, $total)
  	{
		$this->load->language('payment/pp_standard');
		
		if ($this->config->get('pp_standard_total') > $total) {
			return array();
		}
		
		if (!$this->Model_Localisation_Zone->inGeoZone($this->config->get('pp_standard_geo_zone_id'), $address['country_id'], $address['zone_id'])) {
			return array();
		}

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);
		
		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			return array();
		}
					
		$method_data = array(
			'code'		=> 'pp_standard',
			'title'		=> '<img style="position:relative;top:-5px" src="https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif" border="0" alt="" /><span style="position:relative; top:-30px; margin-left:20px;"> OR pay with Credit Card via paypal.</span>',
			'sort_order' => $this->config->get('pp_standard_sort_order'),
		);

		return $method_data;
  	}
}
