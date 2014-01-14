<?php
class Admin_Controller_Sale_Coupon extends Controller
{

	public function index()
	{
		$this->document->setTitle(_l("Coupon"));

		$this->getList();
	}

	public function insert()
	{
		$this->document->setTitle(_l("Coupon"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Coupon->addCoupon($_POST);

			$this->message->add('success', _l("Success: You have modified coupons!"));

			$url = $this->get_url();

			$this->url->redirect('sale/coupon', $url);
		}

		$this->getForm();
	}

	public function update()
	{
		$this->document->setTitle(_l("Coupon"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Sale_Coupon->editCoupon($_GET['coupon_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified coupons!"));

			$url = $this->get_url();

			$this->url->redirect('sale/coupon', $url);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->document->setTitle(_l("Coupon"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $coupon_id) {
				$this->Model_Sale_Coupon->deleteCoupon($coupon_id);
			}

			$this->message->add('success', _l("Success: You have modified coupons!"));

			$url = $this->get_url();

			$this->url->redirect('sale/coupon', $url);
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('sale/coupon_list');

		$url_items = array(
			'sort'  => 'name',
			'order' => 'ASC',
			'page'  => 1
		);
		foreach ($url_items as $item => $default) {
			$$item = isset($_GET[$item]) ? $_GET[$item] : $default;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Coupon"), $this->url->link('sale/coupon'));

		$this->data['insert'] = $this->url->link('sale/coupon/insert', $url);
		$this->data['delete'] = $this->url->link('sale/coupon/delete', $url);

		$this->data['coupons'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$coupon_total = $this->Model_Sale_Coupon->getTotalCoupons();

		$results = $this->Model_Sale_Coupon->getCoupons($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => _l("Edit"),
				'href' => $this->url->link('sale/coupon/update', 'coupon_id=' . $result['coupon_id'] . $url)
			);

			$this->data['coupons'][] = array(
				'coupon_id'  => $result['coupon_id'],
				'name'       => $result['name'],
				'code'       => $result['code'],
				'discount'   => $result['discount'],
				'date_start' => $this->date->format($result['date_start'], 'short'),
				'date_end'   => $this->date->format($result['date_end'], 'short'),
				'status'     => ($result['status'] ? _l("Enabled") : _l("Disabled")),
				'selected'   => isset($_GET['selected']) && in_array($result['coupon_id'], $_GET['selected']),
				'action'     => $action
			);
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$sort_list = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
		);
		foreach ($sort_list as $s) {
			$this->data['sort_' . $s] = $this->url->link('sale/coupon', 'sort=' . $s . $url);
		}

		$url = $this->get_url(array(
			'sort',
			'order'
		));

		$this->pagination->init();
		$this->pagination->total  = $coupon_total;
		$this->data['pagination'] = $this->pagination->render();

		$this->data['sort']  = $sort;
		$this->data['order'] = $order;

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function getForm()
	{
		$this->template->load('sale/coupon_form');

		$coupon_id = $this->data['coupon_id'] = isset($_GET['coupon_id']) ? $_GET['coupon_id'] : 0;

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Coupon"), $this->url->link('sale/coupon'));

		if (!$coupon_id) {
			$this->data['action'] = $this->url->link('sale/coupon/insert', $url);
		} else {
			$this->data['action'] = $this->url->link('sale/coupon/update', 'coupon_id=' . $coupon_id . $url);
		}

		$this->data['cancel'] = $this->url->link('sale/coupon', $url);

		if ($coupon_id && !$this->request->isPost()) {
			$coupon_info = $this->Model_Sale_Coupon->getCoupon($coupon_id);
		}

		$defaults = array(
			'name'              => '',
			'code'              => '',
			'type'              => '',
			'discount'          => '',
			'logged'            => '',
			'shipping'          => '',
			'shipping_geozone'  => 0,
			'total'             => '',
			'date_start'        => $this->date->now(),
			'date_end'          => $this->date->now(),
			'uses_total'        => '',
			'uses_customer'     => '',
			'coupon_products'   => array(),
			'coupon_categories' => array(),
			'coupon_customers'  => array(),
			'status'            => 1
		);

		foreach ($defaults as $d => $default) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($coupon_info[$d])) {
				$this->data[$d] = $coupon_info[$d];
			} elseif (!$coupon_id) {
				$this->data[$d] = $default;
			}
		}

		if (!isset($this->data['coupon_products'])) {
			$products = $this->Model_Sale_Coupon->getCouponProducts($coupon_id);

			$this->data['coupon_products'] = array();
			foreach ($products as $product) {
				$this->data['coupon_products'][] = $this->Model_Catalog_Product->getProduct($product['product_id']);
			}
		}

		if (!isset($this->data['coupon_categories'])) {
			$categories = $this->Model_Sale_Coupon->getCouponCategories($coupon_id);

			$this->data['coupon_categories'] = array();
			foreach ($categories as $category_id) {
				$this->data['coupon_categories'][] = $this->Model_Catalog_Category->getCategory($category_id);
			}
		}

		if (!isset($this->data['coupon_customers'])) {
			$customers = $this->Model_Sale_Coupon->getCouponCustomers($coupon_id);

			$this->data['coupon_customers'] = array();
			foreach ($customers as $customer) {
				$this->data['coupon_customers'][] = $this->Model_Sale_Customer->getCustomer($customer['customer_id']);
			}
		}

		if (!isset($this->data['date_start'])) {
			$this->data['date_start'] = date('Y-m-d', strtotime($coupon_info['date_start']));
		}

		if (!isset($this->data['date_end'])) {
			$this->data['date_end'] = date('Y-m-d', strtotime($coupon_info['date_end']));
		}

		$this->data['data_geo_zones'] = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->data['categories'] = $this->Model_Catalog_Category->getCategoriesWithParents();

		$this->data['data_statuses'] = array(
			0 => _l("Disabled"),
			1 => _l("Enabled"),
		);

		$this->data['data_types'] = array(
			'P' => _l("Percent"),
			'F' => _l("Fixed Amount"),
		);

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		//Ajax Urls
		$this->data['url_product_autocomplete'] = $this->url->link('catalog/product/autocomplete');
		$this->data['url_coupon_history']       = $this->url->link('sale/coupon/history');

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'sale/coupon')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify coupons!");
		}

		if ((strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 128)) {
			$this->error['name'] = _l("Coupon Name must be between 3 and 128 characters!");
		}

		if ((strlen($_POST['code']) < 3) || (strlen($_POST['code']) > 10)) {
			$this->error['code'] = _l("Code must be between 3 and 10 characters!");
		}

		$coupon_info = $this->Model_Sale_Coupon->getCouponByCode($_POST['code']);

		if ($coupon_info) {
			if (!isset($_GET['coupon_id'])) {
				$this->error['warning'] = _l("Warning: Coupon code is already in use!");
			} elseif ($coupon_info['coupon_id'] != $_GET['coupon_id']) {
				$this->error['warning'] = _l("Warning: Coupon code is already in use!");
			}
		}

		return $this->error ? false : true;
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'sale/coupon')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify coupons!");
		}

		return $this->error ? false : true;
	}

	public function history()
	{
		$this->template->load('sale/coupon_history');
		$coupon_id = $this->data['coupon_id'] = isset($_GET['coupon_id']) ? $_GET['coupon_id'] : 0;

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$this->data['histories'] = array();

		$results = $this->Model_Sale_Coupon->getCouponHistories($coupon_id, ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $result['amount'],
				'date_added' => $this->date->format($result['date_added'], 'short'),
			);
		}

		$history_total = $this->Model_Sale_Coupon->getTotalCouponHistories($coupon_id);

		$this->pagination->init();
		$this->pagination->total  = $history_total;
		$this->data['pagination'] = $this->pagination->render();


		$this->response->setOutput($this->render());
	}

	private function get_url($override = array())
	{
		$url     = '';
		$filters = !empty($override) ? $override : array(
			'sort',
			'order',
			'page'
		);
		foreach ($filters as $f) {
			if (isset($_GET[$f])) {
				$url .= "&$f=" . $_GET[$f];
			}
		}
		return $url;
	}
}
