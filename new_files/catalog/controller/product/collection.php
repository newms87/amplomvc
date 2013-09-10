<?php
class Catalog_Controller_Product_Collection extends Controller
{
	public function index()
	{
		$this->language->load('product/collection');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_title_all'), $this->url->link('product/collection'));
		
		$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : 0;
		$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
		$attributes = isset($_GET['attribute']) ? $_GET['attribute'] : 0;
		
		$sort_filter = $this->sort->getQueryDefaults('sort_order', 'ASC');
		
		//Display Single Collection Template
		if ($collection_id) {
			$collection_info = $this->Model_Catalog_Collection->getCollection($collection_id);
		
			if (!$collection_info) {
				$this->url->redirect($this->url->link('product/collection'), 302);
			}
			
			$this->template->load('product/collection');
			
			$this->document->setTitle($collection_info['name']);
			$this->document->setDescription($collection_info['meta_description']);
			$this->document->setKeywords($collection_info['meta_keywords']);
			
			if ($collection_info['category_id']) {
				$this->breadcrumb->add($this->Model_Catalog_Category->getCategoryName($collection_info['category_id']), $this->url->link('product/category', 'category_id=' . $collection_info['category_id']));
			}
			
			$this->breadcrumb->add($collection_info['name'], $this->url->link('product/collection', 'collection_id=' . $collection_id));
			
			$this->language->set('head_title', $collection_info['name']);
			
			if ($this->config->get('config_show_collection_image')) {
				$this->data['thumb'] = $this->image->resize($collection_info['image'], $this->config->get('config_image_collection_width'), $this->config->get('config_image_collection_height'));
			}
			
			if ($this->config->get('config_show_collection_description')) {
				$this->data['description'] = html_entity_decode($collection_info['description'], ENT_QUOTES, 'UTF-8');
			} else {
				$this->data['description'] = false;
			}
		
			if ($attributes) {
				$sort_filter['attribute'] = $attributes;
			}
			
			$item_total = $this->Model_Catalog_Collection->getTotalCollectionProducts($collection_id, $sort_filter);
			$products = $this->Model_Catalog_Collection->getCollectionProducts($collection_id, $sort_filter);
			
			if (!empty($products)) {
				if ($this->config->get('config_show_product_list_hover_image')) {
					foreach ($products as &$product) {
						$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
					}
				}

				$params = array(
					'data' => $products,
					'template' => 'block/product/product_list'
				);
				
				$this->data['block_product_list'] = $this->getBlock('product/list', $params);
			}
		}
		//Display Multi Collection Template
		else {
			$this->document->setTitle($this->_('text_title_all'));
			$this->document->setDescription($this->_('text_description_all'));
			$this->document->setKeywords($this->_('text_metakeyword_all'));
			
			$this->template->load('product/collection_list');
						
			$this->language->set('head_title', $this->_('text_name_all'));
			
			$this->data['thumb'] = '';
			
			if ($this->config->get('config_show_collection_description')) {
				$this->data['description'] = $this->_('text_description_all');
			}
			
			if ($category_id) {
				$sort_filter['category_id'] = $category_id;
				
				$this->breadcrumb->add($this->Model_Catalog_Category->getCategoryName($category_id), $this->url->link('product/collection', 'category_id=' . $category_id));
			}
			
			$item_total = $this->Model_Catalog_Collection->getTotalCollections($sort_filter);
			$collections = $this->Model_Catalog_Collection->getCollections($sort_filter);
			
			foreach ($collections as &$collection) {
				$collection['thumb'] = $this->image->resize($collection['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				
				$collection['href'] = $this->url->link("product/collection", 'collection_id=' . $collection['collection_id']);
				
				$collection['description'] = html_entity_decode($collection['description']);
				$collection['price'] = false;
				$collection['rating'] = false;
			}
			
			if (!empty($collections)) {
				$params = array(
					'data' => $collections,
					'template' => 'block/product/product_list'
				);
				
				$this->data['block_collection_list'] = $this->getBlock('product/list', $params);
			}
		}
		
		//Sorting
		$sorts = array(
			'sort=sort_order&order=ASC' => $this->_('text_default'),
			'sort=name&order=ASC' => $this->_('text_name_asc'),
			'sort=name&order=DESC' => $this->_('text_name_desc'),
		);
		
		$this->data['sorts'] = $this->sort->render_sort($sorts);
		
		//List Item Limits
		$this->data['limits'] = $this->sort->render_limit();
		
		//Pagination
		$this->pagination->init();
		$this->pagination->total = $item_total;
	
		$this->data['pagination'] = $this->pagination->render();
		
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
