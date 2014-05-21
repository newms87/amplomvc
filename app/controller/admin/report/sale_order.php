<?php
class App_Controller_Admin_Report_SaleOrder extends Controller
{
	public function index()
	{
		$this->document->setTitle(_l("Sales Report"));

		$query_defaults = array(
			'filter_date_start'      => '',
			'filter_date_end'        => '',
			'filter_group'           => 'week',
			'filter_order_status_id' => 5,
			'page'                   => 1,
		);
		foreach ($query_defaults as $key => $default) {
			$$key = isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		$url = $this->get_url();

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Sales Report"), site_url('report/sale_order'));

		$data['orders'] = array();

		$data = array(
			'filter_date_start'      => $filter_date_start,
			'filter_date_end'        => $filter_date_end,
			'filter_group'           => $filter_group,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * option('config_admin_limit'),
			'limit'                  => option('config_admin_limit')
		);

		$order_total = $this->Model_Report_Sale->getTotalOrders($data);

		$results = $this->Model_Report_Sale->getOrders($data);

		foreach ($results as $result) {
			$data['orders'][] = array(
				'date_start' => $this->date->format($result['date_start'], 'short'),
				'date_end'   => $this->date->format($result['date_end'], 'short'),
				'orders'     => $result['orders'],
				'products'   => $result['products'],
				'tax'        => $this->currency->format($result['tax'], option('config_currency')),
				'total'      => $this->currency->format($result['total'], option('config_currency')),
				'net'        => $this->currency->format($result['total'] - $result['cost'], option('config_currency'))
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

		$url = $this->get_url(array(
			'filter_date_start',
			'filter_date_end',
			'filter_group',
			'filter_order_status_id'
		));

		$this->pagination->init();
		$this->pagination->total  = $order_total;
		$data['pagination'] = $this->pagination->render();

		$data['filter_date_start']      = $filter_date_start;
		$data['filter_date_end']        = $filter_date_end;
		$data['filter_group']           = $filter_group;
		$data['filter_order_status_id'] = $filter_order_status_id;

		$this->response->setOutput($this->render('report/sale_order', $data));
	}

	private function get_url($filters = null)
	{
		$url     = '';
		$filters = $filters ? $filters : array(
			'filter_date_start',
			'filter_date_end',
			'filter_group',
			'filter_order_status_id',
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
