<?php
class Address extends Library
{
	public function format($address)
	{
		static $address_formats = array();
		
		$country_id = $address['country_id'];
		
		if (isset($address_formats[$country_id])) {
			$address_format = $address_formats[$country_id];
		}
		else {
			$address_format = $this->db->queryVar("SELECT address_format FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
			
			if (empty($address_format)) {
				$address_format = 
					"{firstname} {lastname}\n" .
					"{company}\n" .
					"{address_1}\n" .
					"{address_2}\n" .
					"{city}, {zone} {postcode}\n" .
					"{country}";
			}
			
			$address_formats[$country_id] = $address_format;
		}
		
		$insertables = $address;
		
		if (!empty($address['country'])) {
			$insertables['country'] = $address['country']['name'];
			$insertables['iso_code_2'] = $address['country']['iso_code_2'];
			$insertables['iso_code_3'] = $address['country']['iso_code_3'];
		}
		
		if (!empty($address['zone'])) {
			$insertables['zone'] = $address['zone']['name'];
			$insertables['zone_code'] = $address['zone']['code'];
		}
		
		return preg_replace('/<br \/>\s+<br \/>/', '<br />', nl2br($this->tool->insertables($insertables, $address_format, '{', '}')));
	}
}