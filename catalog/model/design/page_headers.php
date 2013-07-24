<?php
class Catalog_Model_Design_PageHeaders extends Model
{
	public function getPageHeader()
	{
		$layout_id = $this->config->get('config_layout_id');
		
		$page_header = $this->query_var("SELECT ph.page_header FROM " . DB_PREFIX . "layout_header lh LEFT JOIN " . DB_PREFIX . "page_header ph ON(ph.page_header_id=lh.page_header_id) WHERE ph.status='1' AND layout_id='$layout_id' AND language_id='". (int)$this->config->get('config_language_id') . "' ORDER BY ph.priority ASC LIMIT 1");
		
		if ($page_header) {
			return html_entity_decode($page_header);
		}
	
		return '';
	}
}