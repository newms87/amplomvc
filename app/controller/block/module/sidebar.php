<?php
/**
 * @author Daniel Newman
 * @date 3/20/2013
 * @package Amplo MVC
 * @link http://amplomvc.com/
 *
 * All Amplo MVC code is released under the GNU General Public License.
 * See COPYING.txt and LICENSE.txt files in the root directory.
 */

/**
 * Class App_Controller_Block_Module_Sidebar
 * Name: Amplo Sidebar
 */
class App_Controller_Block_Module_Sidebar extends App_Controller_Block_Block
{
	public function build($settings)
	{
		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : false;

		$categories = $this->Model_Category->getCategoryTree();

		array_walk_children($categories, 'children', function (&$category) {
			global $registry;
			$category['href'] = $registry->get('url')->link('product/category', 'category_id=' . $category['category_id']);
		});

		$main_menu = $categories;

		$data['main_menu'] = array(
			'label' => _l("Main Menu"),
			'menu'  => $main_menu,
		);

		//Product Attributes Filter
		if ($category_id) {
			$route = 'product/category';

			$current_filter = _get('attribute', array());

			$url_query = $this->url->getQuery('category_id');

			if (!empty($settings['attributes'])) {
				foreach ($settings['attributes'] as $attribute_menu) {
					$attribute_group_id = $attribute_menu['attribute_group_id'];

					$sort = array(
						'name' => 'ASC',
					);

					$filter = array(
						'category_ids'        => array($category_id),
						'attribute_group_ids' => array($attribute_group_id),
					);

					$attribute_list = $this->Model_Catalog_Attribute->getRecords($sort, $filter);

					if (empty($attribute_list)) {
						continue;
					}

					//Setup attribute menu items
					foreach ($attribute_list as &$attribute) {
						//Build Attribute Query
						$attribute_filter                      = $current_filter;
						$attribute_filter[$attribute_group_id] = $attribute['attribute_id'];
						$attribute_query                       = http_build_query(array('attribute' => $attribute_filter));

						$attribute['href'] = site_url($route, $url_query . '&' . $attribute_query);
					}
					unset($attribute);

					//Remove Filter for this attribute for the All menu link
					$attribute_filter = $current_filter;
					unset($attribute_filter[$attribute_group_id]);
					$attribute_query = http_build_query(array('attribute' => $attribute_filter));

					$menu = array(
						'name'     => $attribute_menu['group_name'],
						'href'     => site_url($route, $url_query . '&' . $attribute_query),
						'children' => $attribute_list,
					);

					$data['attribute_menu'][] = array(
						'label' => $attribute_menu['menu_name'],
						'menu'  => array($menu)
					);
				}
			}
		}

		//TODO: move this to admin panel once we implement!
		$page_links = array();

		$data['page_menu'] = array(
			'label' => '',
			'menu'  => $page_links,
		);

		$this->render('block/module/sidebar', $data);
	}

	public function settings(&$settings)
	{
		//The Data
		$data['settings'] = $settings;

		//Template Data
		$data['data_attribute_groups'] = array('' => _l(" --- None --- ")) + $this->Model_Catalog_AttributeGroup->getRecords();

		//Render
		$this->render('block/module/sidebar_settings', $data);
	}
}
