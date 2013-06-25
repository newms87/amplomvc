<?php
class Catalog_Model_Catalog_Information extends Model 
{
	public function getInformation($information_id)
	{
		$query = 
			"SELECT DISTINCT * FROM " . DB_PREFIX . "information i" . 
			" LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)" .
			" WHERE i.information_id = '" . (int)$information_id . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'";
			
		$information = $this->queryRow($query);
		
		$this->translation->translate('information', $information_id, $information);
		
		return $information;
	}
	
	public function getInformations()
	{
		$query = 
			"SELECT * FROM " . DB_PREFIX . "information i" . 
			" LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)" .
			" WHERE AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'" . 
			" ORDER BY i.sort_order, LCASE(i.title) ASC";
		
		$informations = $this->queryRows($query);
		
		$this->translation->translate_all('information', 'information_id', $informations);
		
		return $informations;
	}
	
	public function getInformationLayoutId($information_id)
	{
		return $this->queryVar("SELECT layout_id FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
	}
}