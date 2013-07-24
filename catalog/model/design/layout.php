<?php
class Catalog_Model_Design_Layout extends Model
{
	private $layout_ids = array();
	
	public function get_layout_id($route = null)
	{
		if (!$route) {
			$route = !empty($_GET['route']) ? $_GET['route'] : 'common/home';
		}
		
		if (!isset($this->layout_ids[$route])) {
			$layout_id = false;
			
			//TODO: THere is likely a better way to resolve layouts for these...Maybe get rid of this?
			if (substr($route, 0, 16) == 'product/category' && isset($_GET['path'])) {
				$path = explode('_', (string)$_GET['path']);
					
				$layout_id = $this->Model_Catalog_Category->getCategoryLayoutId(end($path));
			}
			
			if (substr($route, 0, 15) == 'product/product' && isset($_GET['product_id'])) {
				$layout_id = $this->Model_Catalog_Product->getProductLayoutId($_GET['product_id']);
			}
			
			if (substr($route, 0, 23) == 'information/information' && isset($_GET['information_id'])) {
				$layout_id = $this->Model_Catalog_Information->getInformationLayoutId($_GET['information_id']);
			}
			
			if (!$layout_id) {
				$query = $this->query("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE CONCAT(route, '%') AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");
				
				if ($query->num_rows) {
					$layout_id = $query->row['layout_id'];
				}
			}
			
			if (!$layout_id) {
				$layout_id = $this->config->get('config_default_layout_id');
			}
			
			$this->layout_ids[$route] = $layout_id;
		}
		
		return $this->layout_ids[$route];
	}
}