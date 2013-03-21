<?php
final class Tax {
	private $shipping_address;
	private $payment_address;
	private $store_address;
	
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->db = $registry->get('db');	
		$this->session = $registry->get('session');
		
		// If shipping address is being used
		if (isset($this->session->data['shipping_address_id'])) {
			$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->session->data['shipping_address_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
			if ($address_query->num_rows) {
				$this->setShippingAddress($address_query->row['country_id'], $address_query->row['zone_id'], $address_query->row['postcode']);
			}
		} elseif (isset($this->session->data['guest']['shipping_address'])) {
			$this->setShippingAddress($this->session->data['guest']['shipping_address']['country_id'], $this->session->data['guest']['shipping_address']['zone_id'], $this->session->data['guest']['shipping_address']['postcode']);
		} elseif ($this->customer->isLogged() && ($this->config->get('config_tax_customer') == 'shipping')) {
			$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->customer->getAddressId() . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
			if ($address_query->num_rows) {
				$this->setShippingAddress($address_query->row['country_id'], $address_query->row['zone_id'], $address_query->row['postcode']);
			}
		} elseif ($this->config->get('config_tax_default') == 'shipping') {
			$this->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}
		
		if (isset($this->session->data['payment_address_id'])) {
			$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->session->data['payment_address_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
			if ($address_query->num_rows) {
				$this->setPaymentAddress($address_query->row['country_id'], $address_query->row['zone_id'], $address_query->row['postcode']);
			}
		} elseif (isset($this->session->data['guest']['payment_address'])) {
			$this->setPaymentAddress($this->session->data['guest']['payment_address']['country_id'], $this->session->data['guest']['payment_address']['zone_id'], $this->session->data['guest']['payment_address']['postcode']);
		} elseif ($this->customer->isLogged() && ($this->config->get('config_tax_customer') == 'payment')) {
			$address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$this->customer->getPaymentInfo('address_id') . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		
			if ($address_query->num_rows) {
				$this->setPaymentAddress($address_query->row['country_id'], $address_query->row['zone_id'], $address_query->row['postcode']);
			}	
		} elseif ($this->config->get('config_tax_default') == 'payment') {
			$this->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}	
		
		$this->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));	
  	}
	
	public function setShippingAddress($country_id, $zone_id, $postcode=null) {
		$this->shipping_address = array(
			'country_id' => $country_id,
			'zone_id'    => $zone_id,
			'postcode'   => $postcode
		);				
	}

	public function setPaymentAddress($country_id, $zone_id, $postcode=null) {
		$this->payment_address = array(
			'country_id' => $country_id,
			'zone_id'    => $zone_id,
			'postcode'   => $postcode
		);
	}

	public function setStoreAddress($country_id, $zone_id, $postcode=null) {
		$this->store_address = array(
			'country_id' => $country_id,
			'zone_id'    => $zone_id,
			'postcode'   => $postcode
		);
	}
							
  	public function calculate($value, $tax_class_id, $calculate = true) {
		if ($tax_class_id && $calculate) {
			$amount = $this->getTax($value, $tax_class_id);
				
			return $value + $amount;
		} else {
      		return $value;
    	}
  	}
	
  	public function getTax($value, $tax_class_id) {
		$amount = 0;
			
		$tax_rates = $this->getRates($value, $tax_class_id);
		
		foreach ($tax_rates as $tax_rate) {
			$amount += $tax_rate['amount'];
		}
				
		return $amount;
  	}
		
	public function getRateName($tax_rate_id) {
		$tax_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");
	
		if ($tax_query->num_rows) {
			return $tax_query->row['name'];
		} else {
			return false;
		}
	}
	
    public function getRates($value, $tax_class_id) {
		$tax_rates = array();
		
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
				
		if ($this->shipping_address) {
			$this->get_tax_rates($tax_rates, $tax_class_id, 'shipping', $this->shipping_address, $customer_group_id);
		}

		if ($this->payment_address) {
		   $this->get_tax_rates($tax_rates, $tax_class_id, 'payment', $this->payment_address, $customer_group_id);
	   }
		
		if ($this->store_address) {
		   $this->get_tax_rates($tax_rates, $tax_class_id, 'store', $this->store_address, $customer_group_id);
	   }
		
		
		$tax_rate_data = array();
		
		foreach ($tax_rates as $tax_rate) {
			if (isset($tax_rate_data[$tax_rate['tax_rate_id']])) {
				$amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];
			} else {
				$amount = 0;
			}
			
			if ($tax_rate['type'] == 'F') {
				$amount += $tax_rate['rate'];
			} elseif ($tax_rate['type'] == 'P') {
				$amount += ($value / 100 * $tax_rate['rate']);
			}
		
			$tax_rate_data[$tax_rate['tax_rate_id']] = array(
				'tax_rate_id' => $tax_rate['tax_rate_id'],
				'name'        => $tax_rate['name'],
				'rate'        => $tax_rate['rate'],
				'type'        => $tax_rate['type'],
				'amount'      => $amount
			);
		}
		
		return $tax_rate_data;
	}
   
   private function get_tax_rates(&$tax_rates, $tax_class_id, $type, $address, $customer_group_id){
      $tax_query = $this->db->query("SELECT tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1" .
                                    " LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id)" . 
                                    " INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id)" .
                                    " LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id)" .
                                    " LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id)" .
                                    " WHERE tr1.tax_class_id = '" . (int)$tax_class_id . "' AND tr1.based = '$type' AND tr2cg.customer_group_id = '" . (int)$customer_group_id . "'".
                                    " AND z2gz.country_id = '" . (int)$address['country_id'] . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$address['zone_id'] . "') ORDER BY tr1.priority ASC");
      
      //TODO HACK TO APPLY ZONE CODES - SHOULD MOVE THIS TO A NEW TAX TOTAL LINE ITEM!
      $county_tax = array(94022,94024,94035,94040,94041,94043,94085,94086,94087,94089,94301,94303,94304,94305,94306,94550,95002,95008,95013,95014,95020,95023,95030,95032,95033,95035,95037,95046,95050,95051,95053,95054,95070,95076,95110,95111,95112,95113,95116,95117,95118,95119,95120,95121,95122,95123,95124,95125,95126,95127,95128,95129,95130,95131,95132,95133,95134,95135,95136,95138,95139,95140,95141,95148);

      foreach ($tax_query->rows as $result) {
         if(in_array($address['postcode'],$county_tax)){
            $result['rate'] += 1.125;
         }
         $tax_rates[$result['tax_rate_id']] = $result;
         //TODO HACK TO KEEP TAX RATE ACCESSIBLE
         $this->session->data['applied_tax_rates'][$result['tax_rate_id']] = $result;
      }
   }
   
  	public function has($tax_class_id) {
		return isset($this->taxes[$tax_class_id]);
  	}
}
