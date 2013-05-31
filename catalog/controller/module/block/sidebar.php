<?php
class Catalog_Controller_Module_Block_Sidebar extends Controller 
{
	protected function index($settings)
	{
		$this->template->load('module/block/sidebar');
		$this->language->load('module/block/sidebar');
		
		$collection_id = !empty($_GET['collection_id']) ? (int)$_GET['collection_id'] : false;
		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : false;
		
		$main_menu = array();
		
		$main_menu[0] = array(
			'name' => $this->_('text_name_all'),
			'href' => $this->url->link("product/collection"),
		);
		
		if (!$category_id && !$collection_id) {
			$main_menu[0]['attrs'] = array('class ' => 'active');
		}
		
		$collections = $this->Model_Catalog_Collection->getCollectionCategories();
		
		$main_menu += $this->build_collection_menu($collections);
		
		$this->data['main_menu'] = array(
			'label' => $this->_('text_main_menu'),
			'menu' => $main_menu,
		);
		
		//Product Attributes Filter
		if ($collection_id) {
			$current_filter = isset($_GET['attribute']) ? $_GET['attribute'] : array();
			
			$url_query = $this->url->get_query('collection_id', 'category_id');
			
			foreach ($settings['attributes'] as $attribute_menu) {
				$attribute_group_id = $attribute_menu['attribute_group_id'];
				
				if (!$this->Model_Catalog_Collection->hasAttributeGroup($collection_id, $attribute_group_id)) continue;
				
				$attribute_list = $this->Model_Catalog_Product->getAttributeList($attribute_group_id);
				
				//The active attribute_id for this group
				$active_filter = isset($current_filter[$attribute_group_id]) ? $current_filter[$attribute_group_id] : false;
				
				//Setup attribute menu items
				foreach ($attribute_list as &$attribute) {
					//Build Attribute Query
					$attribute_filter = $current_filter;
					$attribute_filter[$attribute_group_id] = $attribute['attribute_id'];
					$attribute_query = http_build_query(array('attribute' => $attribute_filter));
				
					$attribute['href'] = $this->url->link('product/collection', $url_query . '&' . $attribute_query);
	
					if ($active_filter == $attribute['attribute_id']) {
						$attribute['attrs'] = array('class ' => 'active');
					}
				}
				
				//Remove Filter for this attribute for the All menu link
				$attribute_filter = $current_filter;
				unset($attribute_filter[$attribute_group_id]);
				$attribute_query = http_build_query(array('attribute' => $attribute_filter));
				
				$menu = array(
					'name' => $attribute_menu['group_name'],
					'href' => $this->url->link('product/collection', $url_query . '&' . $attribute_query),
					'attrs' => $active_filter ? array() : array('class' => 'active'),
					'children' => $attribute_list,
				);
				
				$this->data['attribute_menu'][] = array(
					'label' => $attribute_menu['menu_name'],
					'menu' => array($menu)
				);
			}
		}
		
		
		//TODO: move this to admin panel once we implement!
		$page_links = array();
		
		$page_links[0] = array(
			'name' => "Sales",
			'href' => $this->url->link('page/page', 'page_id=1')
		);
		
		$this->data['page_menu'] = array(
			'label' => '',
			'menu' => $page_links,
		);
		
		$this->render();
	}

	private function build_collection_menu($collections)
	{
		$category_id = !empty($_GET['category_id']) ? $_GET['category_id'] : false;
		
		$collection_menu = array();
		$parents = array();
		
		//NOTE: This is only a 2 level menu
		foreach ($collections as $collection) {
			
			$menu_item = array(
				'name' => $collection['name'],
				'href' => $this->url->link('product/collection', 'category_id=' . $collection['category_id']),
			);
			
			if ($collection['category_id'] == $category_id) {
				$menu_item['attrs'] = array('class ' => 'active');
			}
			
			if ((int)$collection['parent_id'] == 0) {
				$parents[$collection['category_id']] = $menu_item;
			}
			else {
				$parents[$collection['parent_id']]['children'][] = $menu_item;
			}
		}
		
		return $parents;
	}
}
