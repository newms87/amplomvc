<?php
class App_Controller_Product_Category extends Controller
{
	public function index()
	{
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("All Categories"), site_url('product/category'));

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
			$data['page_title'] = $category_info['name'];

			if (option('config_show_category_image')) {
				$data['thumb'] = $this->image->resize($category_info['image'], option('config_image_category_width'), option('config_image_category_height'));
			}

			if (option('config_show_category_description')) {
				$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			}

			$parents = $this->Model_Catalog_Category->getParents($category_id);

			foreach ($parents as $parent) {
				$this->breadcrumb->add($parent['name'], site_url('product/category', 'category_id=' . $parent['category_id']));
			}

			$this->breadcrumb->add($category_info['name'], site_url('product/category', 'category_id=' . $category_id));
		} else {
			//Page Head
			$this->document->setTitle(_l("All Categories"));
			$this->document->setDescription(_l("All the categories on this site"));
			$this->document->setKeywords(_l("all categories, categories, see list, view all, search, find"));

			//Page Title
			$data['page_title'] = _l("All Categories");

			$data['thumb'] = '';

			$data['description'] = '';
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

			if (option('config_show_product_list_hover_image')) {
				foreach ($products as &$product) {
					$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
				}
			}

			$params = array(
				'data'     => $products,
				'template' => 'block/product/product_list',
			);

			//Load these products in the Product List block template
			$data['block_product_list'] = $this->block->render('product/list', null, $params);

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

			if (option('config_review_status')) {
				$sorts['sort=rating&order=ASC']  = _l("Rating (Lowest)");
				$sorts['sort=rating&order=DESC'] = _l("Rating (Highest)");
			}

			$data['sorts'] = $this->sort->render_sort($sorts);

			$data['limits'] = $this->sort->renderLimits();

			$this->pagination->init();
			$this->pagination->total = $product_total;

			$data['pagination'] = $this->pagination->render();

			//In case there was a problem with the block_product_list
			$data['continue'] = site_url('common/home');
		} else {
			$data['category_name'] = !empty($category_info['name']) ? $category_info['name'] : _l("All Categories");

			$parent = $this->Model_Catalog_Category->getParent($category_id);

			if ($parent && $parent['category_id'] !== 0) {
				$data['continue'] = site_url('product/category', 'category_id=' . $parent['category_id']);
			}
			else {
				$data['continue'] = site_url('common/home');
			}
		}

		//Render
		$this->response->setOutput($this->render('product/category', $data));
	}
}
