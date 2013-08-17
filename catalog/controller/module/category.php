<?php
class Catalog_Controller_Module_Category extends Controller
{
	protected function index($setting)
	{
		$this->template->load('module/category');

		$this->language->load('module/category');

		if (isset($_GET['path'])) {
			$parts = explode('_', (string)$_GET['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$this->data['category_id'] = $parts[0];
		} else {
			$this->data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$this->data['child_id'] = $parts[1];
		} else {
			$this->data['child_id'] = 0;
		}

		$this->data['categories'] = array();

		$categories = $this->Model_Catalog_Category->getCategories(0);

		foreach ($categories as $category) {
			$children_data = array();

			$children = $this->Model_Catalog_Category->getCategories($category['category_id']);

			foreach ($children as $child) {
				$data = array(
					'filter_category_id'  => $child['category_id'],
					'filter_sub_category' => true
				);

				if ($setting['count']) {
					$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name'        => $child['name'] . ' (' . $product_total . ')',
						'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				} else {
					$children_data[] = array(
						'category_id' => $child['category_id'],
						'name'        => $child['name'],
						'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}
			}

			$data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			if ($setting['count']) {
				$product_total = $this->Model_Catalog_Product->getTotalProducts($data);

				$this->data['categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'] . ' (' . $product_total . ')',
					'children'    => $children_data,
					'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			} else {
				$this->data['categories'][] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'],
					'children'    => $children_data,
					'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$this->render();
	}
}
