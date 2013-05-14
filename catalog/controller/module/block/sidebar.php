<?php
class ControllerModuleBlockSidebar extends Controller {
	protected function index($setting) {
		$this->template->load('module/block/sidebar');
		
	   $this->language->load('module/block/sidebar');
      
		$menu_items = array();
	
		$menu_items[0] = array(
			'name' => $this->_('text_name_all'),
			'href' => $this->url->link("product/collection"),
		);
		
		$collections = $this->model_catalog_collection->getCollectionCategories();
		
		$menu_items += $this->build_collection_menu($collections);
		
		$this->data['menu_items'] = $menu_items;
		
		$this->render();
	}

	private function build_collection_menu($collections){
		$collection_menu = array();
		$parents = array();
		
		//NOTE: This is only a 2 level menu
		foreach($collections as $collection){
			
			$menu_item = array(
				'name' => $collection['name'],
				'href' => $this->url->link('product/collection', 'collection_id=' . $collection['collection_id'] . '&' . $collection['category_id']),
			);
			
			if((int)$collection['parent_id'] == 0){
				$parents[$collection['category_id']] = $menu_item;
			}
			else{
				$parents[$collection['parent_id']]['children'][] = $menu_item;
			}
		}
		
		return $parents;
	}
}
