<?php
class Admin_Controller_Report_ProductPurchased extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Products Purchased Report"));

		if (isset($_GET['filter_date_start'])) {
			$filter_date_start = $_GET['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($_GET['filter_date_end'])) {
			$filter_date_end = $_GET['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($_GET['filter_order_status_id'])) {
			$filter_order_status_id = $_GET['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}

		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Products Purchased Report"), site_url('report/product_purchased', $url));

		$data['products'] = array();

		$data = array(
			'filter_date_start'      => $filter_date_start,
			'filter_date_end'        => $filter_date_end,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);

		$product_total = $this->Model_Report_Product->getTotalPurchased($data);

		$results = $this->Model_Report_Product->getPurchased($data);

		foreach ($results as $result) {
			$data['products'][] = array(
				'name'     => $result['name'],
				'model'    => $result['model'],
				'quantity' => $result['quantity'],
				'total'    => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
		}

		$data['order_statuses'] = $this->order->getOrderStatuses();

		$url = '';

		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}

		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}

		$this->pagination->init();
		$this->pagination->total  = $product_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start']      = $filter_date_start;
		$data['filter_date_end']        = $filter_date_end;
		$data['filter_order_status_id'] = $filter_order_status_id;

		$this->response->setOutput($this->render('report/product_purchased', $data));
	}
}
