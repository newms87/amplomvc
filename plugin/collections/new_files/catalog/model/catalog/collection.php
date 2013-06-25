<?php
class Catalog_Model_Catalog_Collection extends Model 
{
	public function getCollection($collection_id)
	{
		$store_id = $this->config->get('config_store_id');
		
		$collection = $this->queryRow(
			"SELECT * FROM " . DB_PREFIX . "collection c" .
			" LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id=cs.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)"  .
			" WHERE cs.store_id='$store_id' AND c.collection_id='" . (int)$collection_id . "' LIMIT 1"
		);
		
		$this->translation->translate('collection', $collection_id, $collection);
		
		return $collection;
	}
	
	public function getCollections($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		} elseif(empty($select)) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "collection c";
		$from .= " LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id = cs.collection_id)";
		
		//Where
		$where = "WHERE cs.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1'";
		
		if (isset($data['category_id'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)";
			
			$where .= " AND cc.category_id='" . (int)$data['category_id'] . "'";
		}
		
		//Order and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$limit = '';
			$order = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from $where $order $limit";
		
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
		
		$collection = $this->queryRow(
			"SELECT c.*, cc.category_id FROM " . DB_PREFIX . "collection c" .
			" LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id=cs.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)" .
			" LEFT JOIN " . DB_PREFIX . "collection_product cp ON (c.collection_id=cp.collection_id)" .
			" WHERE cs.store_id='$store_id' AND cp.product_id='" . (int)$product_id . "' LIMIT 1"
		);
		
		$this->translation->translate('collection', $collection['collection_id'], $collection);
		
		return $collection;
	}
	
	public function getCollectionProducts($collection_id, $data = array(), $select = '', $total = false){
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
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
		
		//Order and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$limit = '';
			$order = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from $where $order $limit";
		
		$result = $this->query($query);
		
		if ($total) {
			return $result->row['total'];
		}
		
		foreach ($result->rows as $key => &$row) {
			$product = $this->Model_Catalog_Product->getProduct($row['product_id']);
			
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
		
		$select = "cc.*, c.parent_id, c.name";
		
		$from = DB_PREFIX . "collection_category cc";
		$from .= " LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = cc.category_id)";
		
		$query = "SELECT $select FROM $from";
		
		$categories = $this->queryRows($query);
		
		$this->translation->translate_all('category', 'category_id', $categories);
		
		return $categories;
	}
	
	public function getTotalCollections($data = array()){
		return $this->getCollections($data, '', true);
	}
	
	public function getTotalCollectionProducts($collection_id, $data = array()){
		return $this->getCollectionProducts($collection_id, $data, '', true);
	}
	
	public function hasAttributeGroup($collection_id, $attribute_group_id)
	{
		$query = 
			"SELECT COUNT(*) FROM " . DB_PREFIX . "collection_product cp" .
			" LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (cp.product_id=pa.product_id)" .
			" LEFT JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id=pa.attribute_id)" .
			" WHERE a.attribute_group_id = '" . (int)$attribute_group_id . "' AND cp.collection_id = '" . (int)$collection_id . "' LIMIT 1";
			
		return $this->queryVar($query);
	}
	
	public function getAttributeList($collection_id, $attribute_group_id)
	{
		$language_id = $this->config->get('config_language_id');
		
		$attributes = $this->cache->get("collection.attribute.list.$collection_id.$attribute_group_id.$language_id");
		
		if (!$attributes) {
			$query =
				"SELECT a.* FROM " . DB_PREFIX . "attribute a" .
				" LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pa.attribute_id=a.attribute_id)" .
				" LEFT JOIN " . DB_PREFIX . "collection_product cp ON (cp.product_id=pa.product_id)" .
				" WHERE a.attribute_group_id = '$attribute_group_id' AND cp.collection_id = '" . (int)$collection_id . "' GROUP BY a.attribute_id ORDER BY name";
			
			$attributes = $this->queryRows($query);
			
			$this->translation->translate_all('attribute', 'attribute_id', $attributes);
			
			$this->cache->set("collection.attribute.list.$collection_id.$attribute_group_id.$language_id",$attributes);
		}
		
		return $attributes;
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
