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
			$product_data = $data + current($data['product_description']);
			
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
					$product_data = $data + current($data['product_description']);
					
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
}
//-----
