<?php

class App_Model_Localisation_Country extends App_Model_Table
{
	protected $table = 'country', $primary_key = 'country_id';
	
	public function addCountry($country)
	{
		clear_cache('country');

		return $this->insert('country', $country);
	}

	public function editCountry($country_id, $country)
	{
		clear_cache('country');

		return $this->update('country', $country, $country_id);
	}

	public function deleteCountry($country_id)
	{
		clear_cache('country');

		return $this->delete('country', $country_id);
	}

	public function getCountry($country_id)
	{
		return $this->queryRow("SELECT * FROM {$this->t['country']} WHERE country_id = " . (int)$country_id);
	}

	public function getCountries($data = array())
	{
		if ($data) {
			$sql = "SELECT * FROM {$this->t['country']}";

			$sort_data = array(
				'name',
				'iso_code_2',
				'iso_code_3'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->query($sql);

			return $query->rows;
		} else {
			$country_data = cache('country');

			if (!$country_data) {
				$query = $this->query("SELECT * FROM {$this->t['country']} ORDER BY name ASC");

				$country_data = $query->rows;

				cache('country', $country_data);
			}

			return $country_data;
		}
	}

	public function getActiveCountries()
	{
		$countries = cache('country.active');

		if (!$countries) {
			$countries = $this->queryRows("SELECT * FROM {$this->t['country']} WHERE status = '1' ORDER BY name ASC");

			cache('country.active', $countries);
		}

		return $countries;
	}

	public function getTotalCountries()
	{
		return $this->queryVar("SELECT COUNT(*) FROM {$this->t['country']}");
	}
}
