<?php
class Catalog_Controller_Product_Product extends Controller 
{
	public function index()
	{
		$this->language->load('product/product');
		
		$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;
		
		$product_info = $this->Model_Catalog_Product->getProduct($product_id);
		
		$this->data['product_info'] = $product_info;
		
		if ($product_info) {
			//Layout Override (only if set)
			$layout_id = $this->Model_Catalog_Product->getProductLayoutId($product_id);
			
			if ($layout_id) {
				$this->config->set('config_layout_id', $layout_id);
			}
			
			$this->data['product_id'] = $product_id;
			
			//Build Breadcrumbs
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			
			$manufacturer_info = $this->Model_Catalog_Manufacturer->getManufacturer($product_info['manufacturer_id']);
			
			if ($manufacturer_info) {
				$this->breadcrumb->add($manufacturer_info['name'], $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']));
			}
			
			$product_info['category'] = $this->Model_Catalog_Category->getCategory($product_info['category_id']);

			$this->breadcrumb->add($product_info['name'], $this->url->link('product/product', 'product_id=' . $product_info['product_id']));
			
			//Setup Document
			$this->document->setTitle($product_info['name']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keywords']);
			
			$this->language->set('heading_title', $product_info['name']);
			
			if ($product_info['template']) {
				$this->template->load('product/' . $product_info['template']);
			}
			else {
				$this->template->load('product/product');
			}
			
			//Product Images
			$this->data['block_product_images'] = $this->getBlock('product/images', array('product_info' => $product_info));
			
			//Product Information
			$this->data['block_product_information'] = $this->getBlock('product/information', array('product_info' => $product_info));
			
			//Additional Information
			$this->data['block_product_additional'] = $this->getBlock('product/additional', array('product_info' => $product_info));
			
			//Find the related products
			$this->data['block_product_related'] = $this->getBlock('product/related', array('product_id' => $product_id));
			
			//The Tags associated with this product
			$tags = $this->Model_Catalog_Product->getProductTags($product_info['product_id']);
			
			foreach ($tags as &$tag) {
				$tag['href'] = $this->url->link('product/search', 'filter_tag=' . $tag['tag']);
			}
			
			$this->_('text_on_store', $this->config->get('config_name'));
			
			$this->data['tags'] = $tags;
			
			if ($product_info['template'] == 'product_video') {
				$this->data['description'] = html_entity_decode($product_info['description']);
			}
		} else {
			$this->url->redirect($this->url->link('error/not_found'));
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
					
		$this->response->setOutput($this->render());
	}
}
