#<?php
//=====
class Admin_Controller_Catalog_Product extends Controller 
{
//.....
	private function getList()
	{
//-----
//>>>>> {php} {before}
		$this->language->plugin('collections', 'admin/product');
		
		$columns['collections'] = array(
			'type' => 'multiselect',
			'display_name' => $this->_('column_collection'),
			'filter' => true,
			'build_config' => array('collection_id' , 'name'),
			'build_data' => $this->Model_Catalog_Collection->getCollections(),
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
		$product['categories'] = $this->Model_Catalog_Product->getProductCategories($product['product_id']);
//-----
//>>>>>
		$product['collections'] = $this->Model_Catalog_Collection->getCollectionsForProduct($product['product_id']);
//-----
//=====
	}
//.....
	private function getForm()
	{
//.....
		$product_info['product_category'] = $this->Model_Catalog_Product->getProductCategories($product_id);
//-----
//>>>>> {php}
		$this->language->plugin('collections', 'admin/product');
		
		$collection_sort = array(
			'sort' => 'name',
			'order' => 'ASC',
		);
		
		$product_info['product_collection'] = $this->Model_Catalog_Collection->getCollectionsForProduct($product_id);
//-----
//=====
		$defaults = array(
//-----
//>>>>> {php}
			'product_collection' => array(),
//-----
//=====
		);
//.....
		$this->data['data_categories'] = $this->Model_Catalog_Category->getCategoriesWithParents();
//-----
//>>>>> {php}
		$collection_sort = array(
			'sort' => 'name',
			'order' => 'ASC',
		);
		
		$this->data['data_collections'] = $this->Model_Catalog_Collection->getCollections($collection_sort);
//-----
//=====
  	}
//.....
}
//-----
