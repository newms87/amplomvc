<?php
class App_Controller_Product_Manufacturer extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Find Your Favorite Brand"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Brand"), site_url('product/manufacturer'));

		$data['categories'] = array();

		$manufacturers = $this->Model_Catalog_Manufacturer->getManufacturers();

		foreach ($manufacturers as $manufacturer) {
			if (is_numeric(substr($manufacturer['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = substr(strtoupper($manufacturer['name']), 0, 1);
			}

			if (!isset($data['manufacturers'][$key])) {
				$data['categories'][$key]['name'] = $key;
			}

			$data['categories'][$key]['manufacturer'][] = array(
				'name' => $manufacturer['name'],
				'href' => site_url('product/manufacturer/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'])
			);
		}

		$data['continue'] = site_url('common/home');

		$this->response->setOutput($this->render('product/manufacturer', $data));
	}

	public function product()
	{
		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Manufacturers"), site_url('product/manufacturer'));

		$manufacturer_id = isset($_GET['manufacturer_id']) ? $_GET['manufacturer_id'] : 0;

		$manufacturer = $this->Model_Catalog_Manufacturer->getManufacturer($manufacturer_id);

		if ($manufacturer) {
			$this->document->setTitle($manufacturer['name']);
			$data['page_title'] = $manufacturer['name'];

			$this->breadcrumb->add($manufacturer['name'], $this->url->here());

			//Sort Data
			$sort = $this->sort->getQueryDefaults('p.name', 'ASC');

			//Filter Data
			$filter = array(
				'manufacturer_ids' => array($manufacturer_id),
			);

			$product_total = $this->Model_Catalog_Product->getTotalProducts($filter);
			$products      = $this->Model_Catalog_Product->getProducts($sort);

			if (option('config_show_product_list_hover_image')) {
				foreach ($products as &$product) {
					$product['images'] = $this->Model_Catalog_Product->getProductImages($product['product_id']);
				}
			}

			$params = array(
				'data'     => $products,
				'template' => 'block/product/product_list',
			);

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

			//Pagination
			$this->pagination->init();
			$this->pagination->total = $product_total;

			$data['pagination'] = $this->pagination->render();
		}

		//Action Buttons
		$data['continue'] = site_url('common/home');

		$this->response->setOutput($this->render('product/category', $data));
	}
}
