<?php
class Catalog_Controller_Block_Module_Sidebar extends Controller
{
	public function index($settings)
	{
		$this->template->load('block/module/sidebar');
		$this->language->load('block/module/sidebar');

		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : false;

		$main_menu = array();

		$main_menu[0] = array(
			'name' => $this->_('text_name_all'),
			'href' => $this->url->link("product/category"),
		);

		$categories = $this->Model_Catalog_Product->getCategoryTree();

		$main_menu += $this->buildCategoryMenu($categories);

		$this->data['main_menu'] = array(
			'label' => $this->_('text_main_menu'),
			'menu' => $main_menu,
		);

		//Product Attributes Filter
		if ($category_id) {
			$route = 'product/category';

			$current_filter = isset($_GET['attribute']) ? $_GET['attribute'] : array();

			$url_query = $this->url->getQuery('category_id');

			foreach ($settings['attributes'] as $attribute_menu) {
				$attribute_group_id = $attribute_menu['attribute_group_id'];

				$attribute_list = $this->Model_Catalog_Category->getAttributeList($category_id, $attribute_group_id);

				if(empty($attribute_list)) {
					continue;
				}

				//Setup attribute menu items
				foreach ($attribute_list as &$attribute) {
					//Build Attribute Query
					$attribute_filter = $current_filter;
					$attribute_filter[$attribute_group_id] = $attribute['attribute_id'];
					$attribute_query = http_build_query(array('attribute' => $attribute_filter));

					$attribute['href'] = $this->url->link($route, $url_query . '&' . $attribute_query);
				}

				//Remove Filter for this attribute for the All menu link
				$attribute_filter = $current_filter;
				unset($attribute_filter[$attribute_group_id]);
				$attribute_query = http_build_query(array('attribute' => $attribute_filter));

				$menu = array(
					'name' => $attribute_menu['group_name'],
					'href' => $this->url->link($route, $url_query . '&' . $attribute_query),
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

		$this->data['page_menu'] = array(
			'label' => '',
			'menu' => $page_links,
		);

		$this->render();
	}

	private function buildCategoryMenu($categories)
	{
		$parents = array();

		//NOTE: This is only a 2 level menu
		foreach ($categories as $category) {

			$menu_item = array(
				'name' => $category['name'],
				'href' => $this->url->link('product/category', 'category_id=' . $category['category_id']),
			);

			if ((int)$category['parent_id'] == 0) {
				$parents[$category['category_id']] = $menu_item;
			}
			else {
				$parents[$category['parent_id']]['children'][] = $menu_item;
			}
		}

		return $parents;
	}
}
