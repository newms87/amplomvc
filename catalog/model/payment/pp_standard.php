<?php
class Catalog_Model_Payment_PpStandard extends Model
{
	public function getMethod($address, $total)
	{
		$this->language->load('payment/pp_standard');

		if ($this->config->get('pp_standard_total') > $total) {
			return array();
		}

		if (!$this->address->inGeoZone($address, $this->config->get('pp_standard_geo_zone_id'))) {
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
			'TRY',
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			return array();
		}

		return $this->data();
	}

	public function data()
	{
		$method_data = array(
			'code'       => 'pp_standard',
			'title'      => "<img src=\"https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif\" border=\"0\" alt=\"Paypal\" />",
			'sort_order' => $this->config->get('pp_standard_sort_order'),
		);

		return $method_data;
	}
}
