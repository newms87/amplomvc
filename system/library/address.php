<?php
class Address extends Library
{
	public function inGeoZone($address, $geo_zone_id)
	{
		if (!$geo_zone_id) {
			return true;
		}
		if (!$address) {
			return false;
		}

		if (!is_array($address)) {
			$address = $this->Model_Account_Address->getAddress($address);
		}

		$geo_zone_id = (int)$geo_zone_id;
		$country_id  = (int)$address['country_id'];
		$zone_id     = (int)$address['zone_id'];

		$include = "0 NOT IN (SELECT COUNT(*) FROM " . DB_PREFIX . "zone_to_geo_zone z2g WHERE g.geo_zone_id = z2g.geo_zone_id AND z2g.country_id IN (0, $country_id) AND z2g.zone_id IN (0, $zone_id))";
		$exclude = "0 IN (SELECT COUNT(*) FROM " . DB_PREFIX . "zone_to_geo_zone z2g2 WHERE g.geo_zone_id = z2g2.geo_zone_id AND z2g2.country_id = '$country_id' AND z2g2.zone_id IN (0, $zone_id))";

		$query = "SELECT COUNT(*) FROM " . DB_PREFIX . "geo_zone g WHERE g.geo_zone_id = '$geo_zone_id' AND IF (g.exclude = '0', $include, $exclude)";

		return $this->db->queryVar($query) > 0;
	}

	public function format($address)
	{
		static $address_formats = array();

		if (!$this->Model_Account_Address->isValidAddress($address)) {
			return '';
		}

		$country_id = $address['country_id'];

		if (isset($address_formats[$country_id])) {
			$address_format = $address_formats[$country_id];
		} else {
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

		//Country Info
		if (empty($address['country'])) {
			$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
		}

		if (!empty($address['country'])) {
			$insertables['country']    = $address['country']['name'];
			$insertables['iso_code_2'] = $address['country']['iso_code_2'];
			$insertables['iso_code_3'] = $address['country']['iso_code_3'];
		}

		//Zone Info
		if (empty($address['zone'])) {
			$address['zone'] = $this->Model_Localisation_Zone->getZone($address['zone_id']);
		}

		if (!empty($address['zone'])) {
			$insertables['zone']      = $address['zone']['name'];
			$insertables['zone_code'] = $address['zone']['code'];
		}

		return preg_replace('/<br \/>\s+<br \/>/', '<br />', nl2br($this->tool->insertables($insertables, $address_format, '{', '}')));
	}
}