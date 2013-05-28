<?php
class ControllerWidgetBlockSpecials extends Controller {
	public function index($settings) {
		$this->template->load('widget/block/specials');
		$this->language->load('widget/block/specials');
		
		$sort_filter = array(
			'has_special' => true,
		);
		
		$this->sort->load_query_defaults($sort_filter, 'price', 'ASC');
		
		$product_total = $this->model_catalog_product->getTotalProducts($sort_filter);
		$products = $this->model_catalog_product->getProducts($sort_filter);
		
		if (!empty($products)) {
			$params = array(
				'data' => $products,
				'template' => 'product/block/product_list',
			);
			
			$this->data['block_product_list'] = $this->getBlock('product','list', $params);
			
			//Sort
			$sorts = array(
				'sort=pd.name&order=ASC' => $this->_('text_name_asc'),
				'sort=pd.name&order=DESC' => $this->_('text_name_desc'),
				'sort=price&order=ASC' => $this->_('text_price_asc'),
				'sort=price&order=DESC' => $this->_('text_price_desc'),
			);
			
			$this->data['sorts'] = $this->sort->render_sort($sorts);
			
			$this->data['limits'] = $this->sort->render_limit();
			
			$this->pagination->init();
			$this->pagination->total = $product_total;
			
			$this->data['pagination'] = $this->pagination->render();
		}
		
		$this->render();
	}
}