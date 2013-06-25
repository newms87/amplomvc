<?php
class Catalog_Model_Account_Address extends Model 
{
	public function addAddress($data)
	{
		$data['customer_id'] = $this->customer->getId();
		
		$match_data = $data;
		unset($match_data['address_id']);
		
		$existing = $this->addressExists($match_data);
		
		if ($existing) {
			return $existing;
		}
		
		$address_id = $this->insert('address', $data);
		
		return $address_id;
	}
	
	public function editAddress($address_id, $data)
	{
		$this->update('address', $data, array('address_id'=>$address_id, 'customer_id'=>$this->customer->getId()));
	}
	
	public function deleteAddress($address_id)
	{
		$this->delete('address', array('address_id'=>$address_id, 'customer_id'=>$this->customer->getId()));
	}
	
	private function isValidAddress($address)
	{
		if(!trim($address['firstname'].$address['lastname']))return false;
		if(!trim($address['address_1']))return false;
		if(!trim($address['city']))return false;
		if(!(int)$address['zone_id'])return false;
		if(!(int)$address['country_id'])return false;
		return true;
	}
	
	public function addressExists($data)
	{
		$where = $this->get_escaped_values('address', $data, ' AND ');
		
		if (empty($where)) {
			return false;
		}
		
		return $this->queryVar("SELECT address_id FROM " . DB_PREFIX . "address WHERE $where");
	}
	
	public function getAddress($address_id)
	{
		$address = $this->queryRow("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "' LIMIT 1");
		
		if ($address) {
			$this->get_address_localisation($address);
			
			if ($this->isValidAddress($address)) {
				return $address;
			}
			
			$this->deleteAddress($address_id);
		}
		
		return false;
	}
	
	public function getAddresses()
	{
		$address_list = $this->queryRows("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		
		$addresses = array();
		
		foreach ($address_list as $address) {
			$this->get_address_localisation($address);
			
			if ($this->isValidAddress($address)) {
				$addresses[] = $address;
			} else {
				$this->delete( 'address', array('address_id' => $address['address_id'], 'customer_id' => $this->customer->getId()) );
			}
		}
		
		return $addresses;
	}
	
	/**
	 * Adds the country and zone information for the address
	 *
	 * @param &$address - the address array with the country_id and zone_id keys set.
	 */
	private function get_address_localisation(&$address)
	{
		$country = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address['country_id'] . "'");
		
		if ($country) {
			$country_name = $country['name'];
			$iso_code_2 = $country['iso_code_2'];
			$iso_code_3 = $country['iso_code_3'];
			$address_format = $country['address_format'];
		} else {
			$country_name = '';
			$iso_code_2 = '';
			$iso_code_3 = '';
			$address_format = '';
		}
		
		$zone = $this->queryRow("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address['zone_id'] . "'");
		
		if ($zone) {
			$zone_name = $zone['name'];
			$zone_code = $zone['code'];
		} else {
			$zone_name = '';
			$zone_code = '';
		}
		
		$address += array(
			'zone'			=> $zone_name,
			'zone_code'		=> $zone_code,
			'country'		=> $country_name,
			'iso_code_2'	=> $iso_code_2,
			'iso_code_3'	=> $iso_code_3,
			'address_format' => $address_format
		);
	}
	
	public function getTotalAddresses()
	{
		return $this->queryVar("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
	}
}