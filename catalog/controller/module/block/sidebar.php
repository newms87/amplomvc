<?php
class ControllerModuleBlockSidebar extends Controller {
	protected function index($setting) {
		$this->template->load('module/block/sidebar');
		
	   $this->language->load('module/block/sidebar');
      
		$menu_items = array();
	
		$menu_items[0] = array(
			'name' => $this->_('text_name_all'),
			'href' => $this->url->link("product/category"),
		);
		
		$categories = $this->model_catalog_category->getAllCategories();
		
		$menu_items += $this->build_category_menu($categories);
		
		$this->data['menu_items'] = $menu_items;
		
		$this->render();
	}

	private function build_category_menu($categories){
		foreach($categories as $category){
			$category_menu[$category['category_id']] = array(
				'name' => $category['name'],
				'href' => $this->url->link('product/category', 'category_id=' . $category['category_id']),
			);
			
			if(!empty($category['children'])){
				$category_menu[$category['category_id']]['children'] = $this->build_category_menu($category['children']);
			}
		}
		
		return $category_menu;
	}
}
