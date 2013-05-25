<?php
class ControllerWidgetBlockSpecials extends Controller {
	public function index($settings) {
		$this->template->load('widget/block/specials');
		$this->language->load('widget/block/specials');
		
		$data = array(
			'has_special' => true,
		);
		
		$sort_defaults = array(
			'sort' => 'price',
			'order' => 'ASC',
			'page' => 1,
			'limit' => $this->config->get('config_catalog_limit'),
		);
		
		foreach($sort_defaults as $key => $default){
			$data[$key] = isset($_GET[$key]) ? $_GET[$key] : $default;
		}
		
		$data['start'] = ($data['page'] - 1) * $data['limit'];
		
		$product_total = $this->model_catalog_product->getTotalProducts($data);
		$products = $this->model_catalog_product->getProducts($data);
		
		$width = $this->config->get('config_image_product_width');
		$height = $this->config->get('config_image_product_height');
						
		foreach($products as &$product){
			$product['thumb'] = $this->image->resize($product['image'], $width, $height);
		}
		
		$this->data['products'] = $products;
		
		//Sort & Limit
		$this->data['sort_url'] = $this->url->link($_GET['route'], $this->url->get_query_exclude('sort', 'order', 'page'));
		
		$this->data['sort_select'] = $this->url->get_query('sort', 'order');
		
		$this->data['sorts'] = array(
			'sort=pd.name&order=ASC' => $this->_('text_name_asc'),
			'sort=pd.name&order=DESC' => $this->_('text_name_desc'),
			'sort=price&order=ASC' => $this->_('text_price_asc'),
			'sort=price&order=DESC' => $this->_('text_price_desc'),
		);
		
		$this->data['limit_url'] = $this->url->link($_GET['route'],  $this->url->get_query_exclude('limit', 'page'));
		
		$this->data['limits'] = array(
			10 => '10',
			20 => '20',
			50 => '50',
			100 => '100',
			0 => 'all'
		);
		
		$this->data['limit'] = $data['limit'];
		
		$this->pagination->init();
		$this->pagination->total = $product_total;
		$this->pagination->url = $this->url->link($_GET['route'], $this->url->get_query_exclude('page'));
	
		$this->data['pagination'] = $this->pagination->render();
	
		$this->render();
	}
}