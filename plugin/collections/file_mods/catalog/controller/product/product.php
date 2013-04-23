#<?php
//=====
class ControllerProductProduct extends Controller {
//.....
	public function index() {
//-----
//<<<<<
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
         
			if ($manufacturer_info){
			   $this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id'])); 
         }
//-----
//>>>>> {php}
			$collection_info = $this->model_catalog_collection->getCollectionByProduct($product_id);
			
			if($collection_info){
				$this->breadcrumb->add($collection_info['name'], $this->url->link('product/collection', 'collection_id=' . $collection_info['collection_id']));
			}
			else{
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
         
				if ($manufacturer_info){
				   $this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id'])); 
	         }
			}
//-----
//=====
	}
//.....
}
//-----