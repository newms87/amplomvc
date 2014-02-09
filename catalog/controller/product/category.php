<?php
class Catalog_Controller_Product_Category extends Controller
{
	public function index()
	{
		$this->template->load('product/category');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("All Categories"), $this->url->link('product/category'));

		$category_id = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
		$attributes  = isset($_GET['attribute']) ? $_GET['attribute'] : 0;

		$category_info = $this->Model_Catalog_Category->getCategory($category_id);

		if ($category_info) {
			//Layout override (only if set)
			$layout_id = $this->Model_Catalog_Category->getCategoryLayoutId($category_id);

			if ($layout_id) {
				$this->config->set('config_layout_id', $layout_id);
			}

			$this->document->setTitle($category_info['name']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keywords']);

			//Page Title
			$this->data['page_title'] = $category_info['name'];

			if ($this->config->get('config_show_category_image')) {
				$this->data['thumb'] = $this->image->resize($category_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
			}

			if ($this->config->get('config_show_category_description')) {
				$this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			}

			$parents = $this->Model_Catalog_Category->getParents($category_id);

			foreach ($parents as $parent) {
				$this->breadcrumb->add($parent['name'], $this->url->link('product/category', 'category_id=' . $parent['category_id']));
			}

			$this->breadcrumb->add($category_info['name'], $this->url->link('product/category', 'category_id=' . $category_id));
		} else {
			//Page Head
			$this->document->setTitle(_l("All Categories"));
			$this->document->setDescription(_l("All the categories on this site"));
			$this->document->setKeywords(_l("all categories, categories, see list, view all, search, find"));

			//Page Title
			$this->data['page_title'] = _l("All Categories");

			$this->data['thumb'] = '';

			$this->data['description'] = '';
		}

		//TODO: How do we handle sub categories....?

		//Sorting / Filtering
		$sort_by = $category_id ? 'p.sort_order' : 'c.sort_order';
		$sort    = $this->sort->getQueryDefaults($sort_by, 'ASC');

		$filter = array();

		if ($category_id) {
			$filter['category_ids'] = array($category_id);
		}

		if ($attributes) {
			$filter['attribute'] = $attributes;
		}

		$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);

		//Sorted / Filtered Products
		if ($product_total) {
			$products = $this->Model_Catalog_Product->getProducts($sort + $filter);

			if ($this->config->get('config_show_product_list_hover_image')) {
				foreach ($products as &$product) {
					$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
				}
			}

			$params = array(
				'data'     => $products,
				'template' => 'block/product/product_list',
			);

			//Load these products in the Product List block template
			$this->data['block_product_list'] = $this->getBlock('product/list', $params);

			//Sorting
			$sorts = array(
				'sort=p.sort_order&order=ASC' => _l("Default"),
				'sort=p.name&order=ASC'       => _l("Name (A - Z)"),
				'sort=p.name&order=DESC'      => _l("Name (Z - A)"),
				'sort=p.price&order=ASC'      => _l("Price (Low &gt; High)"),
				'sort=p.price&order=DESC'     => _l("Price (High &gt; Low)"),
				'sort=p.model&order=ASC'      => _l("Model (A - Z)"),
				'sort=p.model&order=DESC'     => _l("Model (Z - A)"),
			);

			if ($this->config->get('config_review_status')) {
				$sorts['sort=rating&order=ASC']  = _l("Rating (Lowest)");
				$sorts['sort=rating&order=DESC'] = _l("Rating (Highest)");
			}

			$this->data['sorts'] = $this->sort->render_sort($sorts);

			$this->data['limits'] = $this->sort->renderLimits();

			$this->pagination->init();
			$this->pagination->total = $product_total;

			$this->data['pagination'] = $this->pagination->render();

			//In case there was a problem with the block_product_list
			$this->data['continue'] = $this->url->link('common/home');
		} else {
			$this->data['category_name'] = $category_info['name'];

			$parent = $this->Model_Catalog_Category->getParent($category_id);

			if ($parent && $parent['category_id'] !== 0) {
				$this->data['continue'] = $this->url->link('product/category', 'category_id=' . $parent['category_id']);
			}
			else {
				$this->data['continue'] = $this->url->link('common/home');
			}
		}

		//Dependencies
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//Render
		$this->response->setOutput($this->render());
	}
}
