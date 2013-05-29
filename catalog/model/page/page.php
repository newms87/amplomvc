<?php
class ModelPagePage extends Model
{
	public function getPage($page_id)
	{
		$store_id = $this->config->get('config_store_id');
		
		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON(ps.page_id=p.page_id)" .
			" WHERE p.page_id='" . (int)$page_id . "' AND p.status = '1' AND ps.store_id IN ('-1', '$store_id')";
			
		$page = $this->query_row($query);
		
		$this->translation->translate('page', $page_id, $page);
		
		return $page;
	}
}