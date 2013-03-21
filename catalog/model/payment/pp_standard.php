<?php 
class ModelPaymentPPStandard extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/pp_standard');
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pp_standard_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('pp_standard_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('pp_standard_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
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
			$status = false;
		}			
					
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'pp_standard',
        		'title'      => '<img style="position:relative;top:-5px" src="https://www.paypalobjects.com/en_US/i/btn/btn_xpressCheckout.gif" border="0" alt="" /><span style="position:relative; top:-30px; margin-left:20px;"> OR pay with Credit Card via paypal.</span>',
				'sort_order' => $this->config->get('pp_standard_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
