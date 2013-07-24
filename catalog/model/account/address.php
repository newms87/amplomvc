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
	
	public function isValidAddress($address)
	{
		if(empty($address['firstname']) || empty($address['lastname']) || !trim($address['firstname'].$address['lastname'])) return false;
		if(empty($address['address_1']) || !trim($address['address_1']))return false;
		if(empty($address['city']) || !trim($address['city']))return false;
		if(empty($address['zone_id']))return false;
		if(empty($address['country_id']))return false;
		
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
		$address = $this->queryRow("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");
		
		if (!$this->isValidAddress($address)) {
			$this->deleteAddress($address_id);
			
			return null;
		}
		
		$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
		$address['zone'] = $this->Model_Localisation_Zone->getZone($address['zone_id']);
		
		return $address;
	}
	
	public function getAddresses($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "address a";
		
		//Where
		$where = "1";
		
		if (!empty($data['customer_ids'])) {
			$where .= " AND a.customer_id IN (" . implode(',', $data['customer_ids']) . ")";
		}

		if (!empty($data['country_ids'])) {
			$where .= " AND a.country_id IN (0, " . implode(',', $data['country_ids']) . ")";
		}
		
		if (!empty($data['zone_ids'])) {
			$where .= " AND a.zone_id IN (0, " . implode(',', $data['zone_ids']) . ")";
		}
		
		//Order By and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if($total) {
			return $result->row['total'];
		}
		
		$addresses = array();
		
		foreach ($result->rows as $address) {
			if ($this->isValidAddress($address)) {
				$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
				$address['zone'] = $this->Model_Localisation_Zone->getZone($address['zone_id']);
				
				$addresses[] = $address;
			} else {
				$this->delete( 'address', array('address_id' => $address['address_id']) );
			}
		}
		
		return $addresses;
	}
	
	public function getTotalAddresses($data = array())
	{
		return $this->getAddresses($data, '', true);
	}
}