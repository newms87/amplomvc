#<?php
//=====
class Catalog_Controller_Product_Product extends Controller
{
//.....
	public function index()
	{
//-----
//<<<<<
		$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($product_info['manufacturer_id']);

		if ($manufacturer_info && $this->config->get('config_breadcrumbs_show_manufacturer')) {
			$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
		}
//-----
//>>>>> {php}
		$collection_info = $this->Model_Catalog_Collection->getCollectionByProduct($product_id);

		$product_info['collection'] = $collection_info;

		if ($collection_info) {
			$this->language->plugin('collections', 'catalog/product');
			$this->breadcrumb->add($this->_('text_all_collections'), $this->url->link('product/collection'));

			if ($collection_info['category_id']) {
				$this->breadcrumb->add($this->Model_Catalog_Category->getCategoryName($collection_info['category_id']), $this->url->link('product/collection', 'category_id=' . $collection_info['category_id']));
			}

			$this->breadcrumb->add($collection_info['name'], $this->url->link('product/collection', 'collection_id=' . $collection_info['collection_id']));
		}
		else {
			$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
			}
		}
//-----
//=====
		$this->data['display_model']  = $this->config->get('config_show_product_model');
//-----
//>>>>> {php}
		if (!empty($product_info['collection'])) {
			$this->language->plugin('collections', 'catalog/product');
			$collection = $product_info['collection'];

			$collection['href'] = $this->url->link("product/collection", "collection_id=" . $collection['collection_id']);

			$this->_('text_view_more', $collection['href'], $collection['name']);

			$this->data['collection'] = $collection;
		}
//-----
//<<<<<
		$this->_('text_view_more', $this->url->link('product/category', 'category_id=' . $product_info['category']['category_id']), $product_info['category']['name']);
		$this->_('text_keep_shopping', $this->url->link('product/category'));
//-----
//>>>>> {php}
		if (!empty($product_info['collection'])) {
			$this->_('text_view_more', $this->url->link('product/collection', 'collection_id=' . $product_info['collection']['collection_id']), $product_info['collection']['name']);
			$this->_('text_keep_shopping', $this->url->link('product/collection'));
		}
		else {
			$this->_('text_view_more', $this->url->link('product/category', 'category_id=' . $product_info['category']['category_id']), $product_info['category']['name']);
			$this->_('text_keep_shopping', $this->url->link('product/category'));
		}
//-----
//=====
	}
//.....
}
//-----