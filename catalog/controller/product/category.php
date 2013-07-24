<?php
class Catalog_Controller_Product_Category extends Controller
{
	public function index()
	{
		$this->language->load('product/category');
		$this->template->load('product/category');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_all_categories'), $this->url->link('product/category'));
		
		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
		$attributes = isset($_GET['attribute']) ? $_GET['attribute'] : 0;
		
		$category_info = $this->Model_Catalog_Category->getCategory($category_id);
		
		if ($category_info) {
			//Layout override (only if set)
			$layout_id = $this->Model_Catalog_Category->getCategoryLayoutId($category_id);
			
			if ($layout_id) {
				$this->config->set('config_layout_id', $layout_id);
			}
			
			$this->document->setTitle($category_info['name']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keywords']);
			
			$this->language->set('heading_title', $category_info['name']);
			
			if ($this->config->get('config_show_category_image')) {
				$this->data['thumb'] = $this->image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			}
			
			if ($this->config->get('config_show_category_description')) {
				$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			}
			
			$parents = $this->Model_Catalog_Category->getParents($category_id);
			
			foreach ($parents as $parent) {
				$this->breadcrumb->add($parent['name'], $this->url->link('product/category', 'category_id=' . $parent['category_id']));
			}
			
			$this->breadcrumb->add($category_info['name'], $this->url->link('product/category', 'category_id=' . $category_id));
		}
		else {
			$this->document->setTitle($this->_('text_title_all'));
			$this->document->setDescription($this->_('text_description_all'));
			$this->document->setKeywords($this->_('text_metakeyword_all'));
			
			$this->language->set('heading_title', $this->_('text_name_all'));
			
			$this->data['thumb'] = '';
			
			$this->data['description'] = $this->_('text_description_all');
		}
		
		//TODO: How do we handle sub categories....?
		
		//Sorting / Filtering
		$sort = $this->sort->getQueryDefaults('p.sort_order', 'ASC');
		
		$filter = array();
		
		if ($category_id) {
			$filter['category_ids'] = array($category_id);
		}
		
		if ($attributes) {
			$filter['attribute'] = $attributes;
		}
		
		$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);
		
		//Sorted / Filtered Products
		if ($product_total) {
			$products = $this->Model_Catalog_Product->getProducts($sort + $filter);
			
			if ($this->config->get('config_show_product_list_hover_image')) {
				foreach ($products as &$product) {
					$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
				}
			}
				
			$params = array(
				'data' => $products,
				'template' => 'block/product/product_list',
			);
			
			//Load these products in the Product List block template
			$this->data['block_product_list'] = $this->getBlock('product/list', $params);
		
			//Sorting
			$sorts = array(
				'sort=p.sort_order&order=ASC' => $this->_('text_default'),
				'sort=p.name&order=ASC' => $this->_('text_name_asc'),
				'sort=p.name&order=DESC' => $this->_('text_name_desc'),
				'sort=p.price&order=ASC' => $this->_('text_price_asc'),
				'sort=p.price&order=DESC' => $this->_('text_price_desc'),
				'sort=p.model&order=ASC' => $this->_('text_model_asc'),
				'sort=p.model&order=DESC' => $this->_('text_model_desc'),
			);
			
			if ($this->config->get('config_review_status')) {
				$sorts['sort=rating&order=ASC'] = $this->_('text_rating_asc');
				$sorts['sort=rating&order=DESC'] = $this->_('text_rating_desc');
			}
			
			$this->data['sorts'] = $this->sort->render_sort($sorts);
			
			$this->data['limits'] = $this->sort->render_limit();
			
			$this->pagination->init();
			$this->pagination->total = $product_total;
			
			$this->data['pagination'] = $this->pagination->render();
		}
		else {
			$this->_('text_empty', $category_info['name']);
		}
		$this->data['continue'] = $this->url->link('common/home');

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