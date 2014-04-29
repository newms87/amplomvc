<?php
class Catalog_Model_Page_Page extends Model
{
	public function getPage($page_id)
	{
		$store_id = option('config_store_id');

		$query =
			"SELECT * FROM " . DB_PREFIX . "page p" .
			" LEFT JOIN " . DB_PREFIX . "page_store ps ON(ps.page_id=p.page_id)" .
			" WHERE p.page_id='" . (int)$page_id . "' AND p.status = '1' AND ps.store_id IN ('-1', '$store_id')";

		$page = $this->queryRow($query);

		if ($page) {
			$page['content'] = html_entity_decode($page['content']);
			$page['css']     = html_entity_decode($page['css']);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}

	public function getPageForPreview($page_id)
	{
		$page = $this->queryRow("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = " . (int)$page_id);

		if ($page) {
			$page['content'] = html_entity_decode($page['content']);
			$page['css']     = html_entity_decode($page['css']);

			$this->translation->translate('page', $page_id, $page);
		}

		return $page;
	}
}
