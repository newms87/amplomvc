#<?php
//=====
class ControllerProductBlockInformation extends Controller 
{
//.....
	public function index($setting, $product_info)
	{
//-----
//>>>>> {php} {before}
		$collection = $this->Model_Catalog_Collection->getCollectionByProduct($product_info['product_id']);
		if ($collection) {
			$this->language->plugin('collections', 'catalog/product');
			$collection['href'] = $this->url->link("product/collection", "collection_id=" . $collection['collection_id']);
			
			$this->data['collection'] = $collection;
		}
//-----
//=====
		$this->data['manufacturer'] = $product_info['manufacturer'];
//-----
//=====
	}
//.....
}
//-----