#<?php
//=====
class Admin_Model_Catalog_Product extends Model 
{
//.....
	public function addProduct($data)
	{
//-----
//>>>>> {php} {before}
		$this->Model_Catalog_Collection->deleteProductFromCollections($product_id);
		
		if (isset($data['product_collection'])) {
			foreach ($data['product_collection'] as $collection_id) {
				$this->Model_Catalog_Collection->addProductToCollection($collection_id, $product_id, $product_data);
			}
		}
//-----
//=====
		//Additional Product Images
		if (isset($data['product_images'])) {
//.....
		}
//.....
	}
//.....
	public function editProduct($product_id, $data)
	{
//-----
//>>>>> {php} {before}
		if (isset($data['product_collection'])) {
			$collection_list = $this->Model_Catalog_Collection->getCollectionsForProduct($product_id);
			
			$collections = array();
			
			foreach ($collection_list as $collection) {
				$collections[] = $collection['collection_id'];
			}
			
			foreach ($collections as $collection_id) {
				if (!in_array($collection_id, $data['product_collection'])) {
					$this->Model_Catalog_Collection->deleteProductFromCollection($collection['collection_id'], $product_id);
				}
			}
			
			foreach ($data['product_collection'] as $collection_id) {
				if (!in_array($collection_id, $collections)) {
					$this->Model_Catalog_Collection->addProductToCollection($collection_id, $product_id, $product_data);
				}
			}
		}
		else {
			$this->Model_Catalog_Collection->deleteProductFromCollections($product_id);
		}
//-----
//=====
		//Product Additional Images
		$this->delete('product_image', array('product_id'=>$product_id));
//.....
	}
//.....
	public function deleteProduct($product_id)
	{
		$this->delete('product', array('product_id'=>$product_id));
//-----
//>>>>> {php}
		$this->Model_Catalog_Collection->deleteProductFromCollections($product_id);
//-----
//=====
	}
//.....
	public function getProducts($data = array(), $select = '', $total = false)
	{
//.....
		if (isset($data['model'])) {
			$where .= " AND LCASE(p.model) like '%" . strtolower($this->db->escape($data['model'])) . "%'";
		}
//-----
//>>>>> {php}
		if ((isset($data['sort']) && $data['sort'] == 'cp.name') || isset($data['collections'])) {
			$from .= " LEFT JOIN " . DB_PREFIX . "collection_product cp ON (cp.product_id=p.product_id)";
			
			if (!empty($data['collections'])) {
				if (!is_array($data['collections'])) {
					$data['collections'] = array((int)$data['collections']);
				}
				
				$where .= " AND cp.collection_id IN (" . implode(',', $data['collections']) . ")";
			}
		}
//-----
//=====
	}
//.....
}
//-----
