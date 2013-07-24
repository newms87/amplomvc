<?php
class Catalog_Controller_Block_Widget_Specials extends Controller
{
	public function index($settings)
	{
		$this->template->load('block/widget/specials');
		$this->language->load('block/widget/specials');
		
		$sort_filter = array(
			'has_special' => true,
		);
		
		$this->sort->load_query_defaults($sort_filter, 'price', 'ASC');
		
		$product_total = $this->Model_Catalog_Product->getTotalProducts($sort_filter);
		$products = $this->Model_Catalog_Product->getProducts($sort_filter);
		
		if (!empty($products)) {
			$params = array(
				'data' => $products,
				'template' => 'block/product/product_list',
			);
			
			$this->data['block_product_list'] = $this->getBlock('product/list', $params);
			
			//Sort
			$sorts = array(
				'sort=p.name&order=ASC' => $this->_('text_name_asc'),
				'sort=p.name&order=DESC' => $this->_('text_name_desc'),
				'sort=price&order=ASC' => $this->_('text_price_asc'),
				'sort=price&order=DESC' => $this->_('text_price_desc'),
			);
			
			$this->data['sorts'] = $this->sort->render_sort($sorts);
			
			$this->data['limits'] = $this->sort->render_limit();
			
			$this->pagination->init();
			$this->pagination->total = $product_total;
			
			$this->data['pagination'] = $this->pagination->render();
		}
		else {
			$this->data['continue'] = $this->url->link('common/home');
		}
		
		$this->render();
	}
}