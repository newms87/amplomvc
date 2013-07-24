<?php
class Admin_Model_Catalog_ProductClass extends Model
{
	public function addProductClass($data)
	{
		$product_class_id = $this->insert('product_class', $data);
		
		return $product_class_id;
	}
	
	public function editProductClass($product_class_id, $data)
	{
		$this->update('product_class', $data, $product_class_id);
	}
	
	public function deleteProductClass($product_class_id)
	{
		$this->delete('product_class', $product_class_id);
	}
	
	public function getProductClass($product_class_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_class WHERE product_class_id = " . (int)$product_class_id);
	}
	
	public function getProductClasses($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = '*';
		}
		
		//From
		$from = DB_PREFIX . "product_class";
		
		//Where
		$where = "1";
		
		if (!empty($data['product_class_ids'])) {
			$where .= " AND product_class_id IN (" . implode(',', $data['product_class_ids']) . ")";
		}
		
		//Order By and Limit
		if (!$total) {
			$order = $this->extract_order($data);
			$limit = $this->extract_limit($data);
		} else {
			$order = '';
			$limit = '';
		}
		
		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";
		
		$result = $this->query($query);
		
		if($total) {
			return $result->row['total'];
		}
		
		return $result->rows;
	}
	
	public function getTotalProductClasses($data = array())
	{
		return $this->getProductClasses($data, '', true);
	}
}
