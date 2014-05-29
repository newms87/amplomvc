<?php
class App_Controller_Admin_Report_SaleShipping extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Shipping Report"));

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

		if (isset($_GET['filter_group'])) {
			$filter_group = $_GET['filter_group'];
		} else {
			$filter_group = 'week';
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

		if (isset($_GET['filter_group'])) {
			$url .= '&filter_group=' . $_GET['filter_group'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}

		if (isset($_GET['page'])) {
			$url .= '&page=' . $_GET['page'];
		}

		$this->breadcrumb->add(_l("Home"), site_url('admin/common/home'));
		$this->breadcrumb->add(_l("Shipping Report"), site_url('admin/report/sale_shipping', $url));

		$data['orders'] = array();

		$data = array(
			'filter_date_start'      => $filter_date_start,
			'filter_date_end'        => $filter_date_end,
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * option('config_admin_limit'),
			'limit'                  => option('config_admin_limit')
		);

		$order_total = $this->Model_Report_Sale->getTotalShipping($data);

		$results = $this->Model_Report_Sale->getShipping($data);

		foreach ($results as $result) {
			$data['orders'][] = array(
				'date_start' => $this->date->format($result['date_start'], 'short'),
				'date_end'   => $this->date->format($result['date_end'], 'short'),
				'title'      => $result['title'],
				'orders'     => $result['orders'],
				'total'      => $this->currency->format($result['total'], option('config_currency'))
			);
		}

		$data['order_statuses'] = $this->order->getOrderStatuses();

		$data['groups'] = array();

		$data['groups'][] = array(
			'text'  => _l("Years"),
			'value' => 'year',
		);

		$data['groups'][] = array(
			'text'  => _l("Months"),
			'value' => 'month',
		);

		$data['groups'][] = array(
			'text'  => _l("Weeks"),
			'value' => 'week',
		);

		$data['groups'][] = array(
			'text'  => _l("Days"),
			'value' => 'day',
		);

		$url = '';

		if (isset($_GET['filter_date_start'])) {
			$url .= '&filter_date_start=' . $_GET['filter_date_start'];
		}

		if (isset($_GET['filter_date_end'])) {
			$url .= '&filter_date_end=' . $_GET['filter_date_end'];
		}

		if (isset($_GET['filter_group'])) {
			$url .= '&filter_group=' . $_GET['filter_group'];
		}

		if (isset($_GET['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $_GET['filter_order_status_id'];
		}

		$this->pagination->init();
		$this->pagination->total  = $order_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start']      = $filter_date_start;
		$data['filter_date_end']        = $filter_date_end;
		$data['filter_group']           = $filter_group;
		$data['filter_order_status_id'] = $filter_order_status_id;

		$this->response->setOutput($this->render('report/sale_shipping', $data));
	}
}
