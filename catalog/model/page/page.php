<?php
class ModelPagePage extends Model{
	public function getPage($page_id){
		$store_id = $this->config->get('config_store_id');
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "page WHERE store_id='$store_id' AND page_id='" . (int)$page_id . "'");
		
		return $query->row;
	}
}