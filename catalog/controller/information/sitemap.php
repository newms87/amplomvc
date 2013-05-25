<?php  
class ControllerInformationSitemap extends Controller {
	public function index() {
		$this->template->load('information/sitemap');

		$this->language->load('information/sitemap');
 
		$this->document->setTitle($this->_('heading_title')); 

			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('information/sitemap'));

		$this->data['categories'] = array();
					
		$categories_1 = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();
			
			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();
				
				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				
				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'name' => $category_3['name'],
						'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}
				
				$level_2_data[] = array(
					'name'	=> $category_2['name'],
					'children' => $level_3_data,
					'href'	=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])	
				);					
			}
			
			$this->data['categories'][] = array(
				'name'	=> $category_1['name'],
				'children' => $level_2_data,
				'href'	=> $this->url->link('product/category', 'path=' . $category_1['category_id'])
			);
		}
		
		$this->data['special'] = $this->url->link('product/special');
		$this->data['account'] = $this->url->link('account/account');
		$this->data['edit'] = $this->url->link('account/edit');
		$this->data['password'] = $this->url->link('account/password');
		$this->data['address'] = $this->url->link('account/address');
		$this->data['history'] = $this->url->link('account/order');
		$this->data['download'] = $this->url->link('account/download');
		$this->data['cart'] = $this->url->link('cart/cart');
		$this->data['checkout'] = $this->url->link('checkout/checkout');
		$this->data['search'] = $this->url->link('product/search');
		$this->data['contact'] = $this->url->link('information/contact');
		
		$this->data['informations'] = array();
		
		foreach ($this->model_catalog_information->getInformations() as $result) {
				$this->data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id']) 
				);
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