<?php

class App_Model_Address extends App_Model_Table
{
	protected $table = 'address', $primary_key = 'address_id';

	public function save($address_id, $address)
	{
		if ($address_id) {
			$address += $this->getRecord($address_id);
		}

		if (!$this->validate($address)) {
			return false;
		}

		if ($address_id) {
			//Address cannot be edited, therefore, archive the address and create a new one (associating a new address ID to this address)
			if ($this->isLocked($address_id)) {
				$new_address_id = $this->save(null, $address);

				$this->archive($address_id, $new_address_id);

				$address_id = $new_address_id;
			} else {
				$address_id = $this->update('address', $address, $address_id);
			}
		} else {
			$address_id = $this->insert('address', $address);
		}

		return $address_id;
	}

	public function remove($address_id)
	{
		if ($this->isLocked($address_id)) {
			return $this->archive($address_id);
		}

		return $this->delete('address', $address_id);
	}

	public function lock($address_id)
	{
		$this->update('address', array('locked' => 1), $address_id);
	}

	public function isLocked($address_id)
	{
		return $this->queryVar("SELECT locked FROM {$this->t['address']} WHERE address_id = " . (int)$address_id);
	}

	public function exists($address)
	{
		$where = $this->getWhere('address', $address);

		if (empty($where)) {
			return false;
		}

		return $this->queryVar("SELECT address_id FROM {$this->t['address']} WHERE $where");
	}

	/**
	 * Essentially just sets status to 0 for use with orders and subscriptions, and nothing else.
	 **/
	private function archive($address_id, $new_address_id = null)
	{
		$this->update('address', array('status' => 0), $address_id);

		//TODO: This should be handled by customer library. Find new approach.
		if ($new_address_id) {
			$this->queryRows("UPDATE TABLE {$this->t['customer_address']} SET address_id = " . (int)$new_address_id . " WHERE address_id = " . (int)$address_id);
		} else {
			$this->delete('customer_address', $address_id);
		}
	}

	public function inGeoZone($address, $geo_zone_id)
	{
		if (!$address || empty($address['country_id']) || empty($address['zone_id'])) {
			return false;
		}

		//If zero valued, shipping zone is All Zones
		if (!$geo_zone_id) {
			return true;
		}

		if (!is_array($address)) {
			$address = $this->getAddress($address);
		}

		$geo_zone_id = (int)$geo_zone_id;
		$country_id  = (int)$address['country_id'];
		$zone_id     = (int)$address['zone_id'];

		$include = "0 NOT IN (SELECT COUNT(*) FROM {$this->t['zone_to_geo_zone']} z2g WHERE g.geo_zone_id = z2g.geo_zone_id AND z2g.country_id IN (0, $country_id) AND z2g.zone_id IN (0, $zone_id))";
		$exclude = "0 IN (SELECT COUNT(*) FROM {$this->t['zone_to_geo_zone']} z2g2 WHERE g.geo_zone_id = z2g2.geo_zone_id AND z2g2.country_id = '$country_id' AND z2g2.zone_id IN (0, $zone_id))";

		$query = "SELECT COUNT(*) FROM {$this->t['geo_zone']} g WHERE g.geo_zone_id = '$geo_zone_id' AND IF (g.exclude = '0', $include, $exclude)";

		return $this->queryVar($query) > 0;
	}

	public function format($address)
	{
		static $address_formats = array();

		if (!is_array($address)) {
			$address = $this->getAddress($address);
		}

		$address += array(
			'first_name'  => '',
			'last_name'   => '',
			'company'    => '',
			'country_id' => 223,
			'zone_id'    => 0,
			'postcode'   => '',
			'city'       => '',
			'address_1'  => '',
			'address_2'  => '',
		);

		$country_id = $address['country_id'];

		if (isset($address_formats[$country_id])) {
			$address_format = $address_formats[$country_id];
		} else {
			$address_format = $this->queryVar("SELECT address_format FROM {$this->t['country']} WHERE country_id = '" . (int)$country_id . "'");

			if (empty($address_format)) {
				$address_format =
					"{first_name} {last_name}\n";
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

		$address_format = nl2br(insertables($insertables, $address_format, '{', '}'));

		$sr = array(
			"#<br />\\s*<br />#" => '<br />',
			"#^\\s*<br />#" => '',
		);

		do {
			$address_format = preg_replace(array_keys($sr), $sr, $address_format, -1, $count);
		} while($count);

		return $address_format;
	}

	public function validate(&$address)
	{
		$this->error = array();

		if (isset($address['name'])) {
			$name                 = explode(' ', $address['name'], 2);
			$address['first_name'] = $name[0];
			$address['last_name']  = !empty($name[1]) ? $name[1] : '';
		}

		if (isset($address['first_name']) && !validate('text', $address['first_name'], 1, 45)) {
			$this->error['first_name'] = _l("First Name must be less than 45 characters");
		}

		if (isset($address['last_name']) && !validate('text', $address['last_name'], 1, 45)) {
			$this->error['last_name'] = _l("Last Name must be between 3 and 45 characters");
		}

		if (empty($address['address_1'])) {
			if (!empty($address['address'])) {
				$address['address_1'] = $address['address'];
			} else {
				$this->error['address_1'] = _l("Please provide the Street Address.");
			}
		} elseif (!validate('text', $address['address_1'], 3, 128)) {
			$this->error['address_1'] = _l("Address must be between 3 and 128 characters!");
		}

		if (empty($address['city'])) {
			$this->error['city'] = _l("Please provide the city.");
		} elseif (!validate('text', $address['city'], 2, 128)) {
			$this->error['city'] = _l("City must be between 2 and 128 characters!");
		}

		if (!isset($address['country_id'])) {
			$address['country_id'] = option('site_country_id', 223);
		}

		if (empty($address['country_id'])) {
			$this->error['country_id'] = _l("Please select a country.");
		} else {
			$country_info = $this->Model_Localisation_Country->getCountry($address['country_id']);

			if (!$country_info) {
				$this->error['country_id'] = _l("Invalid Country!");
			} elseif ($country_info['postcode_required'] && !validate('text', $address['postcode'], 2, 10)) {
				$this->error['postcode'] = _l("Postcode must be between 2 and 10 characters");
			}
		}

		if (empty($address['zone_id'])) {
			$this->error['zone_id'] = _l("Please select a state.");
		} elseif (!$this->queryVar("SELECT COUNT(*) as total FROM {$this->t['zone']} WHERE zone_id = " . (int)$address['zone_id'] . " AND country_id = " . (int)$address['country_id'])) {
			$this->error['zone_id'] = _l("The Zone does not exist in the selected country!");
		}

		return empty($this->error);
	}
}
