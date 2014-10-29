<?php
class Address extends Library
{
	public function add($address)
	{
		if ($this->validate($address)) {
			return $this->insert('address', $address);
		}

		return false;
	}

	/**
	 * Edits an existing address in the database
	 *
	 * WARNING: This may change the address ID associated for the address being edited to preserve Address Integrity for Transactions, etc.
	 * In the event an address ID is changed, the address ID will be returned, otherwise the return value is null
	 *
	 * @param $address_id - THe ID for the address to edit.
	 * @param $address - The address data, name, street, etc.
	 * @return null | int - If address_id has been changed, then the new address id is returned. Otherwise null.
	 */
	public function edit($address_id, $address)
	{
		//Load full address (in case of missing components) to validate the updated entry
		$address += $this->queryRow("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = " . (int)$address_id);

		if ($this->validate($address)) {
			//Address cannot be edited, therefore, archive the address and create a new one (associating a new address ID to this address)
			if ($this->isLocked($address_id)) {
				$new_address_id = $this->add($address);

				$this->archive($address_id, $new_address_id);

				return $new_address_id;
			}

			$this->update('address', $address, $address_id);

			return true;
		}

		return false;
	}

	public function getAddress($address_id)
	{
		$address = $this->queryRow("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

		if ($address) {
			$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
			$address['zone']    = $this->Model_Localisation_Zone->getZone($address['zone_id']);
		}

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
			$from .= " LEFT JOIN " . DB_PREFIX . "customer_address ca ON (ca.address_id=a.address_id)";

			$where .= " AND ca.customer_id IN (" . implode(',', $data['customer_ids']) . ")";
		}

		if (!empty($data['country_ids'])) {
			$where .= " AND a.country_id IN (0, " . implode(',', $data['country_ids']) . ")";
		}

		if (!empty($data['zone_ids'])) {
			$where .= " AND a.zone_id IN (0, " . implode(',', $data['zone_ids']) . ")";
		}

		if (!empty($data['geo_zones'])) {
			$zones = array();

			foreach ($data['geo_zones'] as $country_id => $country) {
				$zone = "country_id = " . (int)$country_id;

				if (!empty($country['zones']) && !isset($country['zones'][0])) {
					$zone .= " AND a.zone_id IN (" . implode(',', array_keys($country['zones'])) . ")";
				}

				$zones[] = $zone;
			}

			$where .= " AND ((" . implode($zones, ') OR (') . "))";
		}

		if (isset($data['status'])) {
			$where .= " AND a.status = " . $data['status'] ? 1 : 0;
		}

		//Order By and Limit
		list($order, $limit) = $this->extractOrderLimit($data);

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		foreach ($result->rows as &$address) {
			$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
			$address['zone']    = $this->Model_Localisation_Zone->getZone($address['zone_id']);
		}

		return $result->rows;
	}

	public function getTotalAddresses($data = array())
	{
		return $this->getAddresses($data, '', true);
	}

	public function remove($address_id)
	{
		if ($this->isLocked($address_id)) {
			$this->archive($address_id);
		}

		$this->delete('address', $address_id);

		return true;
	}

	public function lock($address_id)
	{
		$this->update('address', array('locked' => 1), $address_id);
	}

	public function isLocked($address_id)
	{
		return $this->queryVar("SELECT locked FROM " . DB_PREFIX . "address WHERE address_id = " . (int)$address_id);
	}

	public function exists($address)
	{
		$where = $this->getWhere('address', $address);

		if (empty($where)) {
			return false;
		}

		return $this->queryVar("SELECT address_id FROM " . DB_PREFIX . "address WHERE $where");
	}

	/**
	 * Essentially just sets status to 0 for use with orders and subscriptions, and nothing else.
	 **/
	private function archive($address_id, $new_address_id = null)
	{
		$this->update('address', array('status' => 0), $address_id);

		//TODO: This should be handled by customer library. Find new approach.
		if ($new_address_id) {
			$this->queryRows("UPDATE TABLE " . DB_PREFIX . "customer_address SET address_id = " . (int)$new_address_id . " WHERE address_id = " . (int)$address_id);
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

		$include = "0 NOT IN (SELECT COUNT(*) FROM " . DB_PREFIX . "zone_to_geo_zone z2g WHERE g.geo_zone_id = z2g.geo_zone_id AND z2g.country_id IN (0, $country_id) AND z2g.zone_id IN (0, $zone_id))";
		$exclude = "0 IN (SELECT COUNT(*) FROM " . DB_PREFIX . "zone_to_geo_zone z2g2 WHERE g.geo_zone_id = z2g2.geo_zone_id AND z2g2.country_id = '$country_id' AND z2g2.zone_id IN (0, $zone_id))";

		$query = "SELECT COUNT(*) FROM " . DB_PREFIX . "geo_zone g WHERE g.geo_zone_id = '$geo_zone_id' AND IF (g.exclude = '0', $include, $exclude)";

		return $this->queryVar($query) > 0;
	}

	public function format($address)
	{
		static $address_formats = array();

		if (!is_array($address)) {
			$address = $this->getAddress($address);
		}

		if (!$this->validate($address)) {
			return '';
		}

		$country_id = $address['country_id'];

		if (isset($address_formats[$country_id])) {
			$address_format = $address_formats[$country_id];
		} else {
			$address_format = $this->queryVar("SELECT address_format FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");

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

	public function validate($address)
	{
		$this->error = array();

		if (isset($address['firstname']) && !validate('text', $address['firstname'], 3, 45)) {
			$this->error['firstname'] = _l("First Name must be between 3 and 45 characters");
		}

		if (isset($address['lastname']) && !validate('text', $address['lastname'], 3, 45)) {
			$this->error['lastname'] = _l("Last Name must be between 3 and 45 characters");
		}

		if (empty($address['address_1'])) {
			$this->error['address_1'] = _l("Please provide the Street Address.");
		} elseif (!validate('text', $address['address_1'], 3, 128)) {
			$this->error['address_1'] = _l("Address must be between 3 and 128 characters!");
		}

		if (empty($address['city'])) {
			$this->error['city'] = _l("Please provide the city.");
		} elseif (!validate('text', $address['city'], 2, 128)) {
			$this->error['city'] = _l("City must be between 2 and 128 characters!");
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

		// Note: Error messages can be changed from Admin Panel based on localization
		if (empty($address['zone_id'])) {
			$this->error['zone_id'] = _l("Please select a state.");
		} elseif (!$this->queryVar("SELECT COUNT(*) as total FROM " . DB_PREFIX . "zone WHERE zone_id = " . (int)$address['zone_id'] . " AND country_id = " . (int)$address['country_id'])) {
			$this->error['zone_id'] = _l("Invalid Zone!");
		}

		return empty($this->error);
	}
}
