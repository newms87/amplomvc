<?php
class Catalog_Model_Localisation_Country extends Model
{
	public function getCountry($country_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
	}

	public function getCountryName($country_id)
	{
		return $this->queryVar("SELECT name FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
	}

	public function getCountries()
	{
		$countries = $this->cache->get('country.status');

		if (!$countries) {
			$countries = $this->queryRows("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");

			$this->cache->set('country.status', $countries);
		}

		return $countries;
	}
}
