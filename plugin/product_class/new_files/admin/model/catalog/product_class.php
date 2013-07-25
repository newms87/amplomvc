<?php
class Admin_Model_Catalog_ProductClass extends Model
{
	public function addProductClass($data)
	{
		$data['front_template'] = serialize($data['front_template']);
		$data['admin_template'] = serialize($data['admin_template']);
		
		$product_class_id = $this->insert('product_class', $data);
		
		return $product_class_id;
	}
	
	public function editProductClass($product_class_id, $data)
	{
		$data['front_template'] = serialize($data['front_template']);
		$data['admin_template'] = serialize($data['admin_template']);
		
		$this->update('product_class', $data, $product_class_id);
	}
	
	public function deleteProductClass($product_class_id)
	{
		$this->delete('product_class', $product_class_id);
	}
	
	public function getProductClass($product_class_id)
	{
		$product_class = $this->queryRow("SELECT * FROM " . DB_PREFIX . "product_class WHERE product_class_id = " . (int)$product_class_id);
		
		$product_class['front_template'] = unserialize($product_class['front_template']);
		$product_class['admin_template'] = unserialize($product_class['admin_template']);
		
		return $product_class;
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
		
		foreach ($result->rows as &$product_class) {
			$product_class['front_template'] = unserialize($product_class['front_template']);
			$product_class['admin_template'] = unserialize($product_class['admin_template']);
		} unset($product_class);
		
		return $result->rows;
	}
	
	public function getTotalProductClasses($data = array())
	{
		return $this->getProductClasses($data, '', true);
	}
	
	public function getFrontTemplates()
	{
		$this->language->load('catalog/product_class');
		
		$front_templates = $this->template->getTemplatesFrom('product', false, $this->_('text_default_template'));
		
		foreach ($front_templates as $theme => &$templates) {
			if ($theme !== 'default') {
				$templates += $front_templates['default'];
			}
		} unset($template);
		
		return $front_templates;
	}
	
	public function getAdminTemplates()
	{
		$this->language->load('catalog/product_class');
		
		$admin_templates = $this->template->getTemplatesFrom('catalog/product_class', true, $this->_('text_default_template'));
		
		foreach ($admin_templates as $theme => &$templates) {
			if ($theme !== 'default') {
				$templates += $admin_templates['default'];
			}
		} unset($template);
		
		return $admin_templates;
	}
}
