<?php
class ModelCatalogCollection extends Model 
{
	public function getCollection($collection_id)
	{
		$store_id = $this->config->get('config_store_id');
		
		$collection = $this->query_row(
			"SELECT * FROM " . DB_PREFIX . "collection c" .
			" LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id=cs.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)"  .
			" WHERE cs.store_id='$store_id' AND c.collection_id='" . (int)$collection_id . "' LIMIT 1"
		);
		
		$this->translation->translate('collection', $collection_id, $collection);
		
		return $collection;
	}
	
	public function getCollections($data = array(), $total = false) {
		if ($total) {
			$select = 'COUNT(*) as total';
		}
		else {
			$select = '*';
		}
		
		$from = DB_PREFIX . "collection c";
		$from .= " LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id = cs.collection_id)";
		
		$where = "WHERE cs.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1'";
		
		if (isset($data['category_id'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)";
			
			$where .= " AND cc.category_id='" . (int)$data['category_id'] . "'";
		}
		
		//ORDER BY and LIMIT
		$order_by = '';
		$limit = '';
		
		if (!$total) {
			if (!empty($data['sort'])) {
				$order = (!empty($data['order']) && $data['order'] == 'DESC') ? 'DESC' : 'ASC';
				
				$order_by = "ORDER BY $data[sort] $order";
			}
			
			$start = !empty($data['start']) ? (int)$data['start'] : 0;
			$limit = !empty($data['limit']) ? (int)$data['limit'] : $this->config->get('config_catalog_limit');
			
			$limit = "LIMIT $start,$limit";
		}
		
		$query = "SELECT $select FROM $from $where $order_by $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		$this->translation->translate_all('collection', 'collection_id', $result->rows);
		
		return $result->rows;
	}
	
	public function getCollectionByProduct($product_id)
	{
		$store_id = $this->config->get('config_store_id');
		
		$collection = $this->query_row(
			"SELECT c.*, cc.category_id FROM " . DB_PREFIX . "collection c" .
			" LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id=cs.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_product cp ON (c.collection_id=cp.collection_id)" .
			" WHERE cs.store_id='$store_id' AND cp.product_id='" . (int)$product_id . "' LIMIT 1"
		);
		
		$this->translation->translate('collection', $collection['collection_id'], $collection);
		
		return $collection;
	}
	
	public function getCollectionProducts($collection_id, $data = array(), $total = false){
		$order_by = '';
		$limit = '';
		
		if ($total) {
			$select = "COUNT(*) as total";
		} else {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "collection_product cp";
		
		//Where
		$where = "WHERE collection_id='" . (int)$collection_id . "'";
		
		if (!empty($data['attribute'])) {
			foreach ($data['attribute'] as $attribute) {
				$table_id = 'pa_' . (int)$attribute;
				
				$from .= " LEFT JOIN " . DB_PREFIX . "product_attribute $table_id ON ($table_id.product_id=cp.product_id)";
				
				$where .= " AND $table_id.attribute_id = '" . (int)$attribute . "'";
			}
		}
		
		//Order By & Limit
		if (!$total) {
			if (!empty($data['sort'])) {
				$order = (!empty($data['order']) && $data['order'] == 'DESC') ? 'DESC' : 'ASC';
				
				$order_by = "ORDER BY $data[sort] $order";
			}
			
			if (!empty($data['limit'])) {
				$start = !empty($data['start']) ? (int)$data['start'] : 0;
				$limit = $data['limit'] > 0 ? (int)$data['limit'] : $this->config->get('config_catalog_limit');
				
				$limit = "LIMIT $start,$limit";
			}
		}
		
		
		$query = "SELECT $select FROM $from $where $order_by $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		foreach ($result->rows as $key => &$row) {
			$product = $this->model_catalog_product->getProduct($row['product_id']);
			
			if ($product) {
				$row += $product;
			} else {
				unset($result->rows[$key]);
			}
		}
		
		return $result->rows;
	}
	
	public function getCollectionCategories()
	{
		$language_id = $this->config->get('config_language_id');
		
		$select = "cc.*, c.parent_id, cd.name";
		
		$from = DB_PREFIX . "collection_category cc";
		$from .= " LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = cc.category_id)";
		$from .= " LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = cc.category_id AND cd.language_id = '$language_id')";
		
		$query = "SELECT $select FROM $from";
		
		$categories = $this->query_rows($query);
		
		$this->translation->translate_all('category', 'category_id', $categories);
		
		return $categories;
	}
	
	public function getTotalCollections($data = array()){
		return $this->getCollections($data, true);
	}
	
	public function getTotalCollectionProducts($collection_id, $data = array()){
		return $this->getCollectionProducts($collection_id, $data, true);
	}
	
	public function hasAttributeGroup($collection_id, $attribute_group_id)
	{
		$query = 
			"SELECT COUNT(*) FROM " . DB_PREFIX . "collection_product cp" .
			" LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (cp.product_id=pa.product_id)" .
			" LEFT JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id=pa.attribute_id)" .
			" WHERE a.attribute_group_id = '" . (int)$attribute_group_id . "' AND cp.collection_id = '" . (int)$collection_id . "' LIMIT 1";
			
		return $this->query_var($query);
	}
	
	public function get_name($product_id)
	{
		$result = $this->query(
			"SELECT cp.name FROM " . DB_PREFIX . "collection c " .
			"JOIN " . DB_PREFIX . "collection_product cp ON (cp.collection_id=c.collection_id) " .
			"WHERE cp.product_id='" . (int)$product_id . "' LIMIT 1"
		);
		
		if ($result->num_rows) {
			return $result->row['name'];
		}
		
		return false;
	}
}
