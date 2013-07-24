<?php
class Catalog_Model_Localisation_Currency extends Model
{
	public function getCurrencyByCode($currency)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");
	}
	
	public function getCurrencies()
	{
		$currencies = $this->cache->get('currency');

		if (!$currencies) {
			$currencies = $this->queryRows("SELECT * FROM " . DB_PREFIX . "currency ORDER BY title ASC");
			
			$this->cache->set('currency', $currencies);
		}
			
		return $currencies;
	}
}