<?php
class ModelDesignLayout extends Model {
	private $layout_ids = array();
   
	public function getLayout($route) {
		if(!isset($this->layout_ids[$route])){
			$query = $this->query("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE CONCAT(route, '%') AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route ASC LIMIT 1");
			if ($query->num_rows) {
				$this->layout_ids[$route] = $query->row['layout_id'];
			} else {
				$this->layout_ids[$route] = 0;
			}
		}
		return $this->layout_ids[$route];
	}
}