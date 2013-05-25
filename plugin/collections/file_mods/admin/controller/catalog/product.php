#<?php
//=====
class ControllerCatalogProduct extends Controller {
//.....
	private function getList() {
//-----
//>>>>> {php} {before}
		$this->language->plugin('collections', 'admin/product');
		
		$columns['collections'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_collection'),
			'filter' => true,
			'build_config' => array('collection_id' => 'name'),
			'build_data' => $this->model_catalog_collection->getCollections(),
			'sortable' => true,
			'sort_value' => 'cp.name',
		);
//-----
//=====
		$columns['categories'] = array(
//.....
		);
//-----
//=====
	}
//.....
	private function getForm() {
//.....
		$defaults = array(
//-----
//>>>>>
		'product_collection' => array(),
//-----
//=====
		);
//.....
		if (!isset($this->data['product_category'])){
			$this->data['product_category'] = $this->model_catalog_product->getProductCategories($product_id);
		}
//-----
//>>>>> {php}
		$this->language->plugin('collections', 'admin/product');
		
		$this->data['data_collections'] = $this->model_catalog_collection->getCollections();
		
		if(!isset($this->data['product_collection'])){
			$this->data['product_collection'] = $this->model_catalog_collection->getCollectionsForProduct($product_id);
		}
//-----
//=====
  	}
//.....
}
//-----
