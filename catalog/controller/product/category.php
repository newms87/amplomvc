<?php
class Catalog_Controller_Product_Category extends Controller 
{
	public function index()
	{
		$this->language->load('product/category');
		$this->template->load('product/category');
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		
		//Sorting / Filtering
		$sort_filter = array();
		
		$this->sort->load_query_defaults($sort_filter, 'sort_order', 'ASC');
		
		$product_total = $this->Model_Catalog_Product->getTotalProducts($sort_filter);
		$products = $this->Model_Catalog_Product->getProducts($sort_filter);
		
		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
		
		$category_info = $this->Model_Catalog_Category->getCategory($category_id);
		
		if ($category_info) {
			$this->document->setTitle($category_info['name']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);
			
			$this->language->set('heading_title', $category_info['name']);
			
			$this->data['thumb'] = $this->image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			
			$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
		}
		else {
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
		
		$results = $this->Model_Catalog_Category->getCategories($category_id);
		
		foreach ($results as $result) {
			$data = array(
				'filter_category_id'  => $result['category_id'],
				'filter_sub_category' => true
			);
			
			$product_total = $this->Model_Catalog_Product->getTotalProducts($data);
			
			$this->data['categories'][] = array(
				'name'  => $result['name'] . ' (' . $product_total . ')',
				'href'  => $this->url->link('product/category', 'category_id=' . $result['category_id'] . '&' . $url)
			);
		}
		
		$sort_filter['category_id'] = $category_id;
		
		$product_total = $this->Model_Catalog_Product->getTotalProducts($sort_filter);
		$products = $this->Model_Catalog_Product->getProducts($sort_filter);
		
		$params = array(
			'data' => $products,
			'template' => 'product/block/product_list',
		);
		
		$this->data['block_product_list'] = $this->getBlock('product', 'list', $params);
		
		//Sorting
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
		
		$this->data['sorts'] = $this->sort->render_sort($sorts);
		
		$this->data['limits'] = $this->sort->render_limit();
		
		$this->pagination->init();
		$this->pagination->total = $product_total;
		
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