#<?php
//=====
class ModelCatalogProduct extends Model {
//.....
	public function addProduct($data) {
//-----
//>>>>> {php} {before}
		$this->model_catalog_collection->deleteProductFromCollections($product_id);
		
      if(isset($data['product_collection'])){
			$product_data = $data + current($data['product_description']);
			
			foreach($data['product_collection'] as $collection_id){
				$this->model_catalog_collection->addProductToCollection($collection_id, $product_id, $product_data);
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
	public function editProduct($product_id, $data) {
//-----
//>>>>> {php} {before}
		$this->model_catalog_collection->deleteProductFromCollections($product_id);
		
      if(isset($data['product_collection'])){
			$product_data = $data + current($data['product_description']);
			
			foreach($data['product_collection'] as $collection_id){
				$this->model_catalog_collection->addProductToCollection($collection_id, $product_id, $product_data);
			}
		}
//-----
//=====
		//Product Additional Images
      $this->delete('product_image', array('product_id'=>$product_id));
//.....
	}
//.....
	public function deleteProduct($product_id) {
		$this->delete('product', array('product_id'=>$product_id));
//-----
//>>>>> {php}
		$this->model_catalog_collection->deleteProductFromCollections($product_id);
//-----
//=====
	}
//.....
}
//-----
