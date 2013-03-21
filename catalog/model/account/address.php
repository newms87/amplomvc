<?php
class ModelAccountAddress extends Model {
	public function addAddress($data) {
	   $data['customer_id'] = $this->customer->getId();
      
      $match_data = $data;
      unset($match_data['address_id']);
      
      $existing = $this->address_exists($match_data);
      
      if($existing){
         return $existing;
      }
      
      $address_id = $this->insert('address', $data);
      
		if ((isset($data['default']) && $data['default'] == '1') || $this->getTotalAddresses() < 1) {
		   $this->customer->set_default_address_id($address_id);
      }
      
		return $address_id;
	}
	
	public function editAddress($address_id, $data) {
	   $this->update('address', $data, array('address_id'=>$address_id, 'customer_id'=>$this->customer->getId()));
	
		if (isset($data['default']) && $data['default'] == '1') {
		   $values = array(
          'address_id' => $address_id
         );
         
         $this->update('customer', $values, $this->customer->getId());
		}
	}
	
	public function deleteAddress($address_id) {
	   $this->delete('address', array('address_id'=>$address_id, 'customer_id'=>$this->customer->getId()));
	}
	
	private function is_valid_address($address){
	   if(!trim($address['firstname'].$address['lastname']))return false;
	   if(!trim($address['address_1']))return false;
      if(!trim($address['city']))return false;
      if(!(int)$address['zone_id'])return false;
      if(!(int)$address['country_id'])return false;
      return true;
   }
   
   public function address_exists($data){
      $query = $this->get('address', 'address_id', $data);
      
      if($query->num_rows){
         return $query->row['address_id'];
      }

      return false;
   }
   
	public function getAddress($address_id) {
		$address_query = $this->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' LIMIT 1");
		
		if ($address_query->num_rows) {
		   $address = $address_query->row;
         
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address['country_id'] . "'");
         
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}
			
			$address += array(
         	'zone'           => $zone,
				'zone_code'      => $code,
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
			
			if(!$this->is_valid_address($address)){
			   $this->deleteAddress($address_id);
            return false; 
         }
         
			return $address;
		} else {
			return false;	
		}
	}
	
	public function getAddresses() {
		$address_data = array();
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		foreach ($query->rows as $result) {
			$country_query = $this->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");
			
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_query = $this->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$code = $zone_query->row['code'];
			} else {
				$zone = '';
				$code = '';
			}		
		   
         $ad = $result;
         
         $ad['zone']           = $zone;
         $ad['zone_code']      = $code;
         $ad['country']        = $country;
         $ad['iso_code_2']     = $iso_code_2;
         $ad['iso_code_3']     = $iso_code_3;
         $ad['address_format'] = $address_format;
         
         if(!$this->is_valid_address($ad)){
            $this->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$result['address_id'] . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
            continue;
         }
         
         $address_data[] = $ad;
		}
		
		return $address_data;
	}	
	
	public function getTotalAddresses() {
		$query = $this->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	
		return $query->row['total'];
	}
}