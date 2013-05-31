<?php
class Catalog_Model_Design_Banner extends Model 
{
	public function getBanner($banner_id)
	{
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "banner_image bi LEFT JOIN " . DB_PREFIX . "banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id) WHERE bi.banner_id = '" . (int)$banner_id . "' AND bid.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY bi.sort_order");
		
		return $query->rows;
	}
}
