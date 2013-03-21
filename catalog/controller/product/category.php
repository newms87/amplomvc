<?php 
class ControllerProductCategory extends Controller {  
	public function index() { 
		$this->language->load('product/category');
		
		$sort_defaults = array(
			'sort' => 'p.sort_order',
			'order' => 'ASC',
			'page' => 1,
			'limit' => $this->config->get('config_catalog_limit'),
		);
		
		foreach($sort_defaults as $key => $default){
			$$key = isset($_GET[$key]) ? $_GET[$key] : $default;
			$this->data[$key] = $$key;
		}
		
		$this->template->load('product/category');

  		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		
		$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : 0;
		
		$category_info = $this->model_catalog_category->getCategory($category_id);
		
		if ($category_info) {
			
			$this->document->setTitle($category_info['name']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			
			$this->language->set('heading_title', $category_info['name']);
			
			$this->data['thumb'] = $this->image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			
			$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
		}
		else{
			$this->document->setTitle($this->_('text_title_all'));
			$this->document->setDescription($this->_('text_description_all'));
			$this->document->setKeywords($this->_('text_metakeyword_all'));
			
			$this->language->set('heading_title', $this->_('text_name_all'));
			
			$this->data['thumb'] = '';
			
			$this->data['description'] = $this->_('text_description_all');
		}
		
		
		//Sub Categories
		$url = $this->url->get_query('sort','order','limit');
		
		$this->data['categories'] = array();
		
		$results = $this->model_catalog_category->getCategories($category_id);
		
		foreach ($results as $result) {
			$data = array(
				'filter_category_id'  => $result['category_id'],
				'filter_sub_category' => true	
			);
						
			$product_total = $this->model_catalog_product->getTotalProducts($data);
			
			$this->data['categories'][] = array(
				'name'  => $result['name'] . ' (' . $product_total . ')',
				'href'  => $this->url->link('product/category', 'category_id=' . $result['category_id'] . '&' . $url)
			);
		}
		
		$this->data['products'] = array();
		
		$data = array(
			'filter_category_id' => $category_id,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);
		
		$product_total = $this->model_catalog_product->getTotalProducts($data); 
		
		$products = $this->model_catalog_product->getProducts($data);
		
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
		
		$url = $this->url->get_query('limit');
		
		$this->data['sort_select'] = $this->url->get_query('sort', 'order');
		$this->data['sort_url'] = $this->url->link('product/category', 'category_id=' . $category_id . '&' . $url);
		
		$this->data['sorts'] = array(
			'sort=p.sort_order&order=ASC' => $this->_('text_default'),
			'sort=pd.name&order=ASC' => $this->_('text_name_asc'),
			'sort=pd.name&order=DESC' => $this->_('text_name_desc'),
			'sort=p.price&order=ASC' => $this->_('text_price_asc'),
			'sort=p.price&order=DESC' => $this->_('text_price_desc'),
			'sort=p.model&order=ASC' => $this->_('text_model_asc'),
			'sort=p.model&order=DESC' => $this->_('text_model_desc'),
		);
		
		if ($this->config->get('config_review_status')) {
			$this->data['sorts']['sort=rating&order=ASC'] = $this->_('text_rating_asc');
			$this->data['sorts']['sort=rating&order=DESC'] = $this->_('text_rating_desc');
		}
		
		$url = $this->url->get_query('sort','order');
		
		$this->data['limit_url'] = $this->url->link('product/category', 'category_id=' . $category_id . '&' . $url . '&limit=');
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
		$this->pagination->total = $product_total;
		$this->pagination->page = $page;
		$this->pagination->limit = $limit;
		$this->pagination->text = $this->_('text_pagination');
		$this->pagination->url = $this->url->link('product/category', 'category_id=' . $category_id . '&' . $url . '&page={page}');
	
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