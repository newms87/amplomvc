<?php
class Catalog_Controller_Product_Manufacturer extends Controller
{
	public function index()
	{
		$this->template->load('product/manufacturer');
		$this->language->load('product/manufacturer');

		$this->document->setTitle($this->_('head_title'));

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_brand'), $this->url->link('product/manufacturer'));

		$this->data['categories'] = array();

		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();

		foreach ($manufacturers as $manufacturer) {
			if (is_numeric(substr($manufacturer['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = substr(strtoupper($manufacturer['name']), 0, 1);
			}

			if (!isset($this->data['manufacturers'][$key])) {
				$this->data['categories'][$key]['name'] = $key;
			}

			$this->data['categories'][$key]['manufacturer'][] = array(
				'name' => $manufacturer['name'],
				'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
			);
		}

		$this->data['continue'] = $this->url->link('common/home');

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

	public function product()
	{
		$this->language->load('product/manufacturer');
		$this->template->load('product/category');

		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_manufacturer_list'), $this->url->link('product/manufacturer'));

		$manufacturer_id = isset($_GET['manufacturer_id']) ? $_GET['manufacturer_id'] : 0;

		$manufacturer = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);

		if ($manufacturer) {
			$this->document->setTitle($manufacturer['name']);
			$this->language->set('head_title', $manufacturer['name']);

			$this->breadcrumb->add($manufacturer['name'], $this->url->here());

			//Sort Data
			$sort = $this->sort->getQueryDefaults('p.name', 'ASC');

			//Filter Data
			$filter = array(
				'manufacturer_ids' => array($manufacturer_id),
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);
			$products      = $this->Model_Catalog_Product->getProducts($sort);

			if ($this->config->get('config_show_product_list_hover_image')) {
				foreach ($products as &$product) {
					$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
				}
			}

			$params = array(
				'data'     => $products,
				'template' => 'block/product/product_list',
			);

			$this->data['block_product_list'] = $this->getBlock('product/list', $params);

			//Sorting
			$sorts = array(
				'sort=p.sort_order&order=ASC' => $this->_('text_default'),
				'sort=p.name&order=ASC'       => $this->_('text_name_asc'),
				'sort=p.name&order=DESC'      => $this->_('text_name_desc'),
				'sort=p.price&order=ASC'      => $this->_('text_price_asc'),
				'sort=p.price&order=DESC'     => $this->_('text_price_desc'),
				'sort=p.model&order=ASC'      => $this->_('text_model_asc'),
				'sort=p.model&order=DESC'     => $this->_('text_model_desc'),
			);

			if ($this->config->get('config_review_status')) {
				$sorts['sort=rating&order=ASC']  = $this->_('text_rating_asc');
				$sorts['sort=rating&order=DESC'] = $this->_('text_rating_desc');
			}

			$this->data['sorts'] = $this->sort->render_sort($sorts);

			$this->data['limits'] = $this->sort->renderLimits();

			//Pagination
			$this->pagination->init();
			$this->pagination->total = $product_total;

			$this->data['pagination'] = $this->pagination->render();
		}

		//Action Buttons
		$this->data['continue'] = $this->url->link('common/home');

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
