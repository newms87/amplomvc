<?php
class ModelLocalisationCountry extends Model 
{
	public function getCountry($country_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
		
		return $query->row;
	}
	
	public function getCountryName($country_id)
	{
		$query = $this->query("SELECT name FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "' AND status = '1'");
		
		return $query->num_rows?$query->row['name']:'';
	}
	
	public function getCountries()
	{
		$country_data = $this->cache->get('country.status');
		
		if (!$country_data) {
			$query = $this->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");
	
			$country_data = $query->rows;
		
			$this->cache->set('country.status', $country_data);
		}

		return $country_data;
	}
}
