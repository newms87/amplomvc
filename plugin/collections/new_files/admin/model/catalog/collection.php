<?php
class Admin_Model_Catalog_Collection extends Model
{
	public function addCollection($data)
	{
		
		$collection_id = $this->insert('collection', $data);
		
		if (!empty($data['products'])) {
			foreach ($data['products'] as $product) {
				$product['collection_id'] = $collection_id;
				
				$this->insert('collection_product', $product);
			}
		}
		
		if (!empty($data['categories'])) {
			foreach ($data['categories'] as $category_id) {
				$category_data = array(
					'collection_id' => $collection_id,
					'category_id' => $category_id
				);
				
				$this->insert('collection_category', $category_data);
			}
		}
		
		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'collection_id' => $collection_id,
					'store_id' => $store_id
				);
				
				$this->insert('collection_store', $store_data);
			}
		}
		
		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/collection', 'collection_id=' . (int)$collection_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->set_translations('collection', $collection_id, $data['translations']);
		}
		
		$this->cache->delete('collection');
	}
	
	public function addProductToCollection($collection_id, $product_id, $product_data)
	{
		$data = array(
			'collection_id' => $collection_id,
			'product_id' => $product_id,
		);
		
		$data += $product_data;
		
		$data['name'] = $this->filter_name($data['name']);
		
		$this->insert('collection_product', $data);
	}
	
	public function editCollection($collection_id, $data)
	{
		$this->update('collection', $data, $collection_id);
		
		$this->delete('collection_product', array('collection_id' => $collection_id));
		
		if (!empty($data['products'])) {
			foreach ($data['products'] as $product) {
				$product['collection_id'] = $collection_id;
				
				$this->insert('collection_product', $product);
			}
		}
		
		$this->delete('collection_category', array('collection_id' => $collection_id));
		
		if (!empty($data['categories'])) {
			foreach ($data['categories'] as $category_id) {
				$category_data = array(
					'collection_id' => $collection_id,
					'category_id' => $category_id
				);
				
				$this->insert('collection_category', $category_data);
			}
		}
		
		$this->delete('collection_store', array('collection_id' => $collection_id));
		
		if (!empty($data['stores'])) {
			foreach ($data['stores'] as $store_id) {
				$store_data = array(
					'collection_id' => $collection_id,
					'store_id' => $store_id
				);
				
				$this->insert('collection_store', $store_data);
			}
		}

		if (!empty($data['keyword'])) {
			$this->url->setAlias($data['keyword'], 'product/collection', 'collection_id=' . (int)$collection_id);
		} else {
			$this->url->removeAlias('product/collection', 'collection_id=' . (int)$collection_id);
		}
		
		if (!empty($data['translations'])) {
			$this->translation->set_translations('collection', $collection_id, $data['translations']);
		}
		
		$this->cache->delete('collection');
	}
	
	//TODO: make collection append Collection name to products in this collection
	public function filter_name($name)
	{
		return $name;
	}
	
	public function update_field($collection_id, $data)
	{
		$this->update('collection', $data, $collection_id);
	}
	
	public function copyCollection($collection_id)
	{
		$collection = $this->getCollection($collection_id);
		
		unset($collection['category_id']);
		
		$copy_count = $this->db->query_var("SELECT COUNT(*) FROM " . DB_PREFIX . "collection WHERE `name` like '$collection[name]%'");
		$collection['name'] .= ' - Copy (' . $copy_count . ')';
		
		$collection['keyword'] = '';
		
		$collection['categories'] = $this->getCollectionCategoryIds($collection_id);
		$collection['stores'] = $this->getCollectionStoreIds($collection_id);
		
		$this->addCollection($collection);
	}
	
	public function deleteCollection($collection_id)
	{
		$this->delete('collection', $collection_id);
		$this->delete('collection_product', array('collection_id' => $collection_id));
		$this->delete('collection_category', array('collection_id' => $collection_id));
		$this->delete('collection_store', array('collection_id' => $collection_id));
		
		$this->url->removeAlias('product/collection', 'collection_id=' . $collection_id);
		
		$this->translation->delete('collection', $collection_id);
		
		$this->cache->delete('collection');
	}
	
	public function deleteProductFromCollection($collection_id, $product_id)
	{
		$this->delete('collection_product', array('collection_id' => $collection_id, 'product_id' => $product_id));
	}

	public function deleteProductFromCollections($product_id)
	{
		$this->delete('collection_product', array('product_id' => $product_id));
	}
	
	public function getCollection($collection_id)
	{
		$result = $this->query_row("SELECT * FROM " . DB_PREFIX . "collection WHERE collection_id = '" . (int)$collection_id . "'");
		
		$result['keyword'] = $this->url->getAlias('product/collection', 'collection_id=' . (int)$collection_id);
		
		return $result;
	}
	
	public function getCollections($data = array(), $select = null, $total = false) {
		//Select
		if ($total) {
			$select = 'COUNT(*) as total';
		}
		elseif (!$select) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "collection c";
		
		//Where
		$where = 'WHERE 1';
		
		if (isset($data['name'])) {
			$where .= " AND c.name like '%" . $this->db->escape($data['name']) . "%'";
		}
		
		if (!empty($data['categories'])) {
			$category_ids = is_array($data['categories']) ? $data['categories'] : array($data['categories']);
			
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_category cc ON (c.collection_id=cc.collection_id)";
			
			$where .= " AND cc.category_id IN (" . implode(',', $category_ids) . ")";
		}
		
		if (!empty($data['stores'])) {
			$store_ids = is_array($data['stores']) ? $data['stores'] : array($data['stores']);
			
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_store cs ON (c.collection_id=cs.collection_id)";
			
			$where['AND'][] = "cs.store_id IN (" . implode(',', $store_ids) . ")";
		}
		
		if (isset($data['status'])) {
			$where .= " AND c.status = '" . ($data['status'] ? 1 : 0) . "'";
		}
		
		//Order By & Limit
		if (!$total) {
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
		if ($total) {
			return $result->row['total'];
		}
	
		return $result->rows;
	}
	
	public function getCollectionsForProduct($product_id)
	{
		$result = $this->query(
			"SELECT c.*, cs.store_id FROM " . DB_PREFIX . "collection c" .
			" LEFT JOIN " . DB_PREFIX ."collection_store cs ON (c.collection_id=cs.collection_id)" .
			" WHERE c.collection_id IN (SELECT cp.collection_id FROM " . DB_PREFIX . "collection_product cp WHERE cp.product_id='" . (int)$product_id . "')"
		);
		
		return $result->rows;
	}
	
	public function getCollectionProducts($collection_id)
	{
		return $this->query_rows("SELECT * FROM " . DB_PREFIX . "collection_product WHERE collection_id = '" . (int)$collection_id . "'");
	}
	
	public function getCollectionCategories($collection_id)
	{
		return $this->query_rows("SELECT * FROM " . DB_PREFIX . "collection_category WHERE collection_id = '" . (int)$collection_id . "'");
	}
	
	public function getCollectionCategoryIds($collection_id)
	{
		$categories = $this->query_rows("SELECT category_id FROM " . DB_PREFIX . "collection_category WHERE collection_id = '" . (int)$collection_id . "'");
		
		return array_column($categories, 'category_id');
	}
	
	public function getCollectionStores($collection_id)
	{
		return $this->query_rows("SELECT * FROM " . DB_PREFIX . "collection_store WHERE collection_id = '" . (int)$collection_id . "'");
	}
	
	public function getCollectionStoreIds($collection_id)
	{
		$stores = $this->query_rows("SELECT store_id FROM " . DB_PREFIX . "collection_store WHERE collection_id = '" . (int)$collection_id . "'");
		
		return array_column($stores, 'store_id');
	}
	
	public function getTotalCollections($data = array()) {
		return $this->getCollections($data, null, true);
	}
}
