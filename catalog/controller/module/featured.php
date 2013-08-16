<?php
class Catalog_Controller_Module_Featured extends Controller
{
	protected function index($setting)
	{
		$this->language->load('module/featured');

		$this->data['products'] = array();

		$filter_types = $this->config->get('product_filter_types');

		if (isset($_GET['sort_by'])) {
			$active_filter = $_GET['sort_by'];
		} elseif (isset($_POST['sort_by'])) {
			$active_filter = $_POST['sort_by'];
		} else {
			$active_filter = $this->config->get('default_product_filter');
		}

		if (isset($_GET['category_id'])) {
			$cat_filter = $_GET['category_id'];
		} elseif (isset($_POST['category_id'])) {
			$cat_filter = $_POST['category_id'];
		} else {
			$cat_filter = 0;
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} elseif (isset($_POST['page'])) {
			$page = $_POST['page'];
		} else {
			$page = 1;
		}

		if (isset($setting['fm_id'])) {
			$this->template->load('module/featured_menu');

			$this->language->set('head_title', $this->_('head_title_menu'));
			$this->language->set('category_title', $this->_('category_title_menu'));

			foreach ($filter_types as &$type) {
				$type = htmlentities($type, ENT_QUOTES);
			}

			$this->data['menu_items']   = $filter_types;
			$this->data['selected']     = $active_filter;
			$this->data['selected_cat'] = $cat_filter;

			$this->data['menu_indent'] = 10;

			$this->data['categories'] = array(
				0           => array(
					'category_id' => 0,
					'name'        => 'All'
				),
				'spotlight' => array(
					'category_id' => -1,
					'name'        => "Betty's Daily Spotlight"
				)
			);
			foreach ($this->Model_Catalog_Category->getAllCategories() as $cat) {
				$this->data['categories'][$cat['category_id']] = $cat;
			}
		} else {
			/* Popup window */
			$this->data['sort_by']     = $active_filter;
			$this->data['category_id'] = $cat_filter;
			$this->data['page']        = $page;
			$this->data['filter_url']  = $this->url->link('module/featured/filter');
			$this->data['display']     = $setting['display'];

			/* In Context Stuff */
			if ($setting['display'] == 'context') {
				($limit = $setting['limit']) ? '' : $limit = 8;

				$setting['limit']        = 4;
				$this->data['spotlight'] = $this->getChild('module/featured/filter', array(
				                                                                          'setting'  => $setting,
				                                                                          'filter'   => 'featured',
				                                                                          'category' => -1
				                                                                     ));

				$setting['limit']          = 8;
				$this->data['ending_soon'] = $this->getChild("module/featured/filter", array(
				                                                                            'setting'  => $setting,
				                                                                            'filter'   => 'ending_soon',
				                                                                            'category' => 0
				                                                                       ));

			}
		}

		$this->render();
	}

	public function filter($args = array())
	{
		$this->template->load('module/featured_results');

		$setting    = null;
		$in_context = false;
		if (!empty($args)) {
			$setting    = $args['setting'];
			$in_context = true;
		} else {
			foreach ($this->config->get('featured_module') as $module) {
				if ($module['layout_id'] == $this->config->get('config_default_layout_id')) {
					$setting = $module;
					break;
				}
			}
		}
		if (!$setting) {
			$setting = array(
				'limit'        => 8,
				'image_width'  => 174,
				'image_height' => 135
			);
		}

		$this->language->load('module/featured');
		$filter_types = $this->config->get('product_filter_types');

		$this->data['products'] = array();

		if ($in_context) {
			$active_filter = isset($args['filter']) ? $args['filter'] : $this->config->get('default_product_filter');
			$cat_filter    = isset($args['category']) ? $args['category'] : 0;
			$page          = 1;
		} else {
			$active_filter = isset($_POST['sort_by']) ? $_POST['sort_by'] : $this->config->get('default_product_filter');
			$cat_filter    = isset($_POST['category_id']) ? $_POST['category_id'] : 0;
			$page          = isset($_POST['page']) ? $_POST['page'] : 1;
		}

		//This is a hack for the special case category Betty's Daily Spotlight
		if ($cat_filter == -1) {
			$this->data['featured_cat']   = 'Spotlight';
			$this->data['featured_title'] = "Betty's Daily";
			$active_filter                = 'featured';
			$cat_filter                   = 0;
		} elseif ($cat_filter) {
			$cat                          = $this->Model_Catalog_Category->getCategory($cat_filter);
			$this->data['featured_cat']   = $cat['name'];
			$this->data['featured_title'] = $filter_types[$active_filter];
		} else {
			$this->data['featured_cat']   = '';
			$this->data['featured_title'] = $filter_types[$active_filter];

		}

		$products = array();
		$products = $this->Model_Catalog_Product->getFilteredProducts($active_filter, $cat_filter, $setting['limit'], $page);

		if (!$in_context) {
			$this->data['total_products'] = $this->Model_Catalog_Product->getFilteredProducts($active_filter, $cat_filter, -1);
			$this->data['product_limit']  = $setting['limit'];
			$this->data['page']           = $page;
			$this->data['num_pages']      = ceil($this->data['total_products'] / $setting['limit']);
		}

		foreach ($products as $product_info) {
			if ($product_info['image']) {
				$image = $this->image->resize($product_info['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}

			if (($this->config->get('config_customer_hide_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_hide_price')) {
				$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id']));
			} else {
				$price = false;
			}

			if ((float)$product_info['special']) {
				$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id']));
			} else {
				$special = false;
			}

			$this->data['products'][] = array(
				'product_id'   => $product_info['product_id'],
				'thumb'        => $image,
				'name'         => $this->tool->limit_characters($product_info['name'], 50),
				'price'        => $price,
				'special'      => $special,
				'flashsale_id' => $special ? $product_info['flashsale_id'] : null,
				'is_final'     => $product_info['is_final'],
				'href'         => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
			);
		}

		//if true, will display only the products on the featured result template
		$this->data['products_only'] = $page > 1;

		if (!$in_context) {
			$this->data['ajax_loader'] = $this->image->get('data/ajax-loader.gif');
		}


		$this->data['in_context'] = $in_context;

		$this->response->setOutput($this->render());
	}
}
