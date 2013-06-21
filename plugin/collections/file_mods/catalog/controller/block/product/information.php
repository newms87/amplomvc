#<?php
//=====
class Catalog_Controller_Block_Product_Information extends Controller 
{
//.....
	public function index($settings)
	{
//-----
//>>>>> {php} {before}
		if (!empty($product_info['collection'])) {
			$this->language->plugin('collections', 'catalog/product');
			$collection = $product_info['collection'];
			
			$collection['href'] = $this->url->link("product/collection", "collection_id=" . $collection['collection_id']);
			
			$this->_('text_view_more', $collection['href'], $collection['name']);
			
			$this->data['collection'] = $collection;
		}
//-----
//=====
		$this->data['manufacturer'] = $product_info['manufacturer'];
//-----
//<<<<<
		$this->_('text_view_more', $this->url->link('product/category', 'category_id=' . $product_info['category']['category_id']), $product_info['category']['name']);
		$this->_('text_keep_shopping', $this->url->link('product/category')); 
//-----
//>>>>>
		$this->_('text_view_more', $this->url->link('product/collection', 'collection_id=' . $product_info['collection']['collection_id']), $product_info['collection']['name']);
		$this->_('text_keep_shopping', $this->url->link('product/collection'));
//-----
//=====
	}
//.....
}
//-----