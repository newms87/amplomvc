<?php 
class ControllerProductCollection extends Controller {  
	public function index() { 
		$this->language->load('product/collection');
		
		$sort_defaults = array(
			'sort' => 'sort_order',
			'order' => 'ASC',
			'page' => 1,
			'limit' => $this->config->get('config_catalog_limit'),
		);
		
		foreach($sort_defaults as $key => $default){
			$this->data[$key] = $$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$this->template->load('product/collection');

  		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_title_all'), $this->url->link('product/collection'));
		
		$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : 0;
		$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
			
		if($collection_id){
			$collection_info = $this->model_catalog_collection->getCollection($collection_id);
		
			if (!$collection_info) {
				$this->url->redirect($this->url->link('product/collection'), 302);
			}
			
			$this->template->load('product/collection');
			
			$this->document->setTitle($collection_info['name']);
			$this->document->setDescription($collection_info['meta_description']);
			$this->document->setKeywords($collection_info['meta_keywords']);
			
			if($collection_info['category_id']){
				$this->breadcrumb->add($this->model_catalog_category->getCategoryName($collection_info['category_id']), $this->url->link('product/collection', 'category_id=' . $collection_info['category_id']));
			}
			
			$this->breadcrumb->add($collection_info['name'], $this->url->link('product/collection', 'collection_id=' . $collection_id));
			
			$this->language->set('heading_title', $collection_info['name']);
			
			$this->data['thumb'] = $this->image->resize($collection_info['image'], $this->config->get('config_image_collection_width'), $this->config->get('config_image_collection_height'));
			
			$this->data['description'] = html_entity_decode($collection_info['description'], ENT_QUOTES, 'UTF-8');
			
			$this->data['products'] = array();
		
			$data = array(
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);
			
			$item_total = $this->model_catalog_collection->getTotalCollectionProducts($collection_id, $data);
			
			$products = $this->model_catalog_collection->getCollectionProducts($collection_id, $data);
			
			$show_price_tax = $this->config->get('config_show_price_with_tax');
			
			foreach ($products as &$product) {
				$product['thumb'] = $this->image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				
				if(($this->config->get('config_customer_price') ? $this->customer->isLogged() : true)){
					$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $show_price_tax));
					if($product['special']){
						$product['special'] = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $show_price_tax));
					}
					else{
						$product['special'] = false;
					}
				} else {
					$product['price'] = false;
					$product['special'] = $product['special'] ? true : false;
				}
				
				if ($show_price_tax) {
					$product['tax'] = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price']);
				} else {
					$product['tax'] = false;
				}				
				
				if ($this->config->get('config_review_status')) {
					$product['rating'] = (int)$product['rating'];
					$product['reviews'] = sprintf($this->_('text_reviews'), (int)$product['reviews']);
				} else {
					$product['rating'] = false;
				}
				
				$product['description'] = substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..';
				
				$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']); 
			}
	
			$this->data['products'] = $products;
		}
		else{
			$this->document->setTitle($this->_('text_title_all'));
			$this->document->setDescription($this->_('text_description_all'));
			$this->document->setKeywords($this->_('text_metakeyword_all'));
			
			$this->template->load('product/collection_list');
						
			$this->language->set('heading_title', $this->_('text_name_all'));
			
			$this->data['thumb'] = '';
			
			$this->data['description'] = $this->_('text_description_all');
			
			$data = array(
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);
			
			if($category_id){
				$data['category_id'] = $category_id;
				
				$this->breadcrumb->add($this->model_catalog_category->getCategoryName($category_id), $this->url->link('product/collection', 'category_id=' . $category_id));
			}
			
			$item_total = $this->model_catalog_collection->getTotalCollections($data);
			$collections = $this->model_catalog_collection->getCollections($data);
			
			foreach($collections as &$collection){
				$collection['thumb'] = $this->image->resize($collection['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				
				$collection['href'] = $this->url->link("product/collection", 'collection_id=' . $collection['collection_id']);
				
				$collection['description'] = html_entity_decode($collection['description']);
				$collection['price'] = false;
				$collection['rating'] = false;
			}
			
			$this->data['collections'] = $collections;
		}
		
		$url = $this->url->get_query('category_id', 'limit');
		
		$this->data['sort_select'] = $this->url->get_query('sort', 'order');
		$this->data['sort_url'] = $this->url->link('product/collection', 'collection_id=' . $collection_id . '&' . $url);
		
		$this->data['sorts'] = array(
			'sort=sort_order&order=ASC' => $this->_('text_default'),
			'sort=name&order=ASC' => $this->_('text_name_asc'),
			'sort=name&order=DESC' => $this->_('text_name_desc'),
		);
		
		$query = $this->url->get_query('category_id', 'sort','order');
		
		$query_collection = $collection_id ? '&collection_id=' . $collection_id : '';
		$this->data['limit_url'] = $this->url->link('product/collection', $query . $query_collection . '&limit=');
		
		$this->data['limits'] = array(
			10 => '10',
			20 => '20',
			50 => '50',
			100 => '100',
			0 => 'all'
		);
		
		$this->language->format('text_compare', (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		
		$this->data['compare'] = $this->url->link('product/compare');
		
		$url = $this->url->get_query('sort', 'order', 'limit');

		$this->pagination->init();
		$this->pagination->total = $item_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $limit;
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('product/collection', 'collection_id=' . $collection_id . '&' . $url . '&page={page}');
	
		$this->data['pagination'] = $this->pagination->render();
	
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
	
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
