<?php
class ModelPagePage extends Model {
	public function addPage($data) {
		
		$page_id = $this->insert('page', $data);
		
		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'page_id' => $page_id,
					'store_id' => $store_id
				);
				
				$this->insert('page_store', $store_data);
			}
		}
		
		if (!empty($data['keyword'])) {
			$this->url->set_alias($data['keyword'], 'page/page', 'page_id=' . (int)$page_id);
		}
		
		if(!empty($data['translations'])){
			$this->translation->set_translations('page', $page_id, $data['translations']);
		}
		
		$this->cache->delete('page');
	}
	
	public function editPage($page_id, $data) {
		$this->update('page', $data, $page_id);
		
		$this->delete('page_store', array('page_id' => $page_id));
		
		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'page_id' => $page_id,
					'store_id' => $store_id
				);
				
				$this->insert('page_store', $store_data);
			}
		}

		if ($data['keyword']) {
			$this->url->set_alias($data['keyword'], 'page/page', 'page_id=' . (int)$page_id);
		}
		
		if(!empty($data['translations'])){
			$this->translation->set_translations('page', $page_id, $data['translations']);
		}
		
		$this->cache->delete('page');
	}
	
	public function update_field($page_id, $data){
		$this->update('page', $data, $page_id);
	}
	
	public function copyPage($page_id){
		$page = $this->getPage($page_id);
		$page['stores'] = $this->getPageStores($page_id);
		
		$this->addPage($page);
	}
	
	public function deletePage($page_id) {
		$this->delete('page', $page_id);
		$this->delete('page_store', array('page_id' => $page_id));
		
		$this->url->remove_alias('page/page', 'page_id=' . $page_id);
		
		$this->translation->delete('page', $page_id);
		
		$this->cache->delete('page');
	}
	
	public function getPage($page_id) {
		$result = $this->query_row("SELECT * FROM " . DB_PREFIX . "page WHERE page_id = '" . (int)$page_id . "'");
		
		$result['keyword'] = $this->url->get_alias('page/page', 'page_id=' . (int)$page_id);
		
		//Translations
		$translate_fields = array(
			'name',
			'meta_keywords',
			'meta_description',
			'content',
		);
		
		$result['translations'] = $this->translation->get_translations('page', $page_id, $translate_fields);
		
		return $result;
	}
	
	public function getPages($data = array(), $select = null, $total = false) {
		//Select
		if($total){
			$select = 'COUNT(*) as total';
		}
		elseif(!$select){
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "page p";
		
		//Where
		$where = 'WHERE 1';
		
		if(isset($data['name'])){
			$where .= " AND c.name like '%" . $this->db->escape($data['name']) . "%'";
		}
		
		if(!empty($data['stores'])){
			$store_ids = is_array($data['stores']) ? $data['stores'] : array($data['stores']);
			
			$from .= " LEFT JOIN " . DB_PREFIX . "page_store cs ON (c.page_id=cs.page_id)";
			
			$where .= " AND cs.store_id IN (" . implode(',', $store_ids) . ")";
		}
		
		if(isset($data['status'])){
			$where .= " AND c.status = '" . ($data['status'] ? 1 : 0) . "'";
		}
		
		//Order By & Limit
		if(!$total){
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$sql = "SELECT $select FROM $from $where $order $limit";
		
		//Execute
		$result = $this->query($sql);
		
		//Process Results
		if($total){
			return $result->row['total'];
		}
	
		return $result->rows;
	}
	
	public function getPageStores($page_id) {
		return $this->query_rows("SELECT * FROM " . DB_PREFIX . "page_store WHERE page_id = '" . (int)$page_id . "'");
	}
	
	public function getTotalPages($data = array()) {
		return $this->getPages($data, null, true);
	}
}
