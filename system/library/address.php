<?php

class Address extends Library
{
	public function format($address)
	{
		static $address_formats = array();

		if (!is_array($address)) {
			$address = $this->Model_Address->getRecord($address);
		}

		$address += array(
			'first_name' => '',
			'last_name'  => '',
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
					"{first_name} {last_name}\n" .
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
		if (option('site_international')) {
			if (empty($address['country'])) {
				$address['country'] = $this->Model_Localisation_Country->getCountry($address['country_id']);
			}

			if (!empty($address['country'])) {
				$insertables['country']    = $address['country']['name'];
				$insertables['iso_code_2'] = $address['country']['iso_code_2'];
				$insertables['iso_code_3'] = $address['country']['iso_code_3'];
			}
		} else {
			$insertables['country'] = '';
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
			"#^\\s*<br />#"      => '',
		);

		do {
			$address_format = preg_replace(array_keys($sr), $sr, $address_format, -1, $count);
		} while ($count);

		return $address_format;
	}
}
